<?php

namespace App\Controllers;

use App\Domain\Content\ValueObjects\TopicFile;
use App\Models\Video as VideoFile;
use App\Domain\Content\Entities\Video as VideoEntity;
use App\Domain\Content\Entities\Topic as TopicEntity;
use App\Domain\Content\ValueObjects\PageFile as PageFileVO;
use App\Domain\File\Entities\File as FileEntity;
use App\Models\VideoQuality;
use App\Services\Log;
use App\Services\Redis;
use App\Services\VideoCache;
use Illuminate\Database\Capsule\Manager;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Stream;
use Throwable;
use Vesp\Controllers\Controller;

class Video extends Controller
{
    protected ?VideoEntity $video = null;
    protected VideoCache $cache;
    protected Redis $redis;

    public function __construct(Manager $eloquent, VideoCache $cache, Redis $redis)
    {
        parent::__construct($eloquent);
        $this->cache = $cache;
        $this->redis = $redis;
    }

    public function checkScope(string $method): ?ResponseInterface
    {
        if ($method === 'options') {
            return parent::checkScope('options');
        }

        return $this->loadFile();
    }

    protected function loadFile(): ?ResponseInterface
    {
        $uuid = $this->getProperty('uuid');
        if (!$videoModel = VideoFile::query()->find($uuid)) {
            return $this->failure('Not Found', 404);
        }

        $this->video = new VideoEntity(
            $videoModel->id,
            $videoModel->title,
            $videoModel->description,
            $videoModel->duration,
            $videoModel->active,
            $videoModel->pageFiles->map(fn($pageFile) => new PageFileVO($pageFile->file, $pageFile))->toArray(),
            $videoModel->topicFiles->map(fn($topicFile) => new TopicFile(
                new TopicEntity($topicFile->topic->uuid, $topicFile->topic->title, $topicFile->topic->content, $topicFile->topic->user_id),
                new FileEntity($topicFile->file)
            ))->toArray()
        );

        $cacheTTL = (int)getenv('CACHE_MEDIA_ACCESS_TIME') ?: 3600;
        $key = implode(':', ['video', $uuid, $this->user?->id ?: 'null']);

        $allow = $this->redis->get($key);
        if ($allow !== null) {
            return $allow ? null : $this->failure('Access Denied', 403);
        }

        $isAdmin = $this->user && $this->user->hasScope('videos/patch');
        if (!$isAdmin && !$this->video->isActive()) {
            return $this->failure('Not Found', 404);
        }

        $allow = $isAdmin || $this->video->hasPageFiles('video');
        if (!$allow && $this->video->hasTopicFilesWithAccess('video', $this->user)) {
            $allow = true;
        }
        $this->redis->set($key, $allow, 'EX', $cacheTTL);
        return $allow ? null : $this->failure('Access Denied', 403);
    }

    public function get(): ResponseInterface
    {
        if ($quality = $this->getProperty('quality')) {
            if ($quality === 'chapters') {
                return $this->success($this->video->chapters);
            }

            if ($quality === 'thumbnails') {
                return $this->getThumbnails();
            }

            if ($quality === 'download' && getenv('DOWNLOAD_MEDIA_ENABLED')) {
                return $this->download($this->video->file);
            }

            /** @var VideoQuality $videoQuality */
            if ($videoQuality = $this->video->qualities()->where('quality', $quality)->first()) {
                if ($range = $this->request->getHeaderLine('Range')) {
                    return $this->getRange($videoQuality->file, $range);
                }

                return $this->getQuality($videoQuality);
            }
        } elseif ($response = $this->getManifest()) {
            return $response;
        }

        return $this->failure('Not Found', 404);
    }

    public function getManifest(): ?ResponseInterface
    {
        if (!$manifest = $this->video->manifest) {
            $manifest = $this->video->getManifest();
        }
        if (empty($manifest)) {
            return null;
        }

        $this->response->getBody()->write($manifest);

        return $this->response
            ->withHeader('Accept-Ranges', 'bytes')
            ->withHeader('Content-Type', 'audio/mpegurl')
            ->withHeader('Content-Length', $this->response->getBody()->getSize())
            ->withHeader(
                'Access-Control-Allow-Origin',
                getenv('CORS') ? $this->request->getHeaderLine('HTTP_ORIGIN') : ''
            );
    }

    public function getThumbnails(): ?ResponseInterface
    {
        if (!$this->video->thumbnails && $this->video->thumbnail_id) {
            $this->video->thumbnails = $this->video->getThumbnails();
            $this->video->save();
        }

        return $this->success($this->video->thumbnails);
    }

    protected function getQuality(VideoQuality $videoQuality): ResponseInterface
    {
        $manifest = $videoQuality->manifest;
        $fs = $videoQuality->file->getFilesystem();
        if (getenv('DOWNLOAD_MEDIA_FROM_S3') && method_exists($fs, 'getStreamLink')) {
            $link = $fs->getStreamLink($videoQuality->file->getFilePathAttribute());
            $manifest = preg_replace('#^' . $videoQuality->quality . '$#m', $link, $manifest);
        }
        $this->response->getBody()->write($manifest);

        return $this->response
            ->withHeader('Accept-Ranges', 'bytes')
            ->withHeader('Content-Type', 'audio/mpegurl')
            ->withHeader('Content-Length', $this->response->getBody()->getSize())
            ->withHeader(
                'Access-Control-Allow-Origin',
                getenv('CORS') ? $this->request->getHeaderLine('HTTP_ORIGIN') : ''
            );
    }

    protected function getRange(File $file, string $ranges): ResponseInterface
    {
        $range = explode('=', $ranges);
        [$start, $end] = array_map('intval', explode('-', end($range), 2));
        if (!$end) {
            $tmp = $start + 1048576; // 1 Mb
            $end = $tmp + 1 >= $file->size ? $file->size - 1 : $tmp;
        }
        if ($end - $start >= 1073741824) {
            return $this->failure('Range Not Satisfiable', 416);
        }

        try {
            $fs = $file->getFilesystem();
            if (method_exists($fs, 'readRangeStream')) {
                if (!getenv('CACHE_S3_SIZE') || !$data = $this->cache->get($file->uuid, $start, $end)) {
                    /** @var \Psr\Http\Message\StreamInterface $body */
                    $body = $fs->readRangeStream($file->getFilePathAttribute(), $start, $end);
                    $this->response = $this->response->withBody($body);
                    $length = $body->getSize();
                    if (getenv('CACHE_S3_SIZE')) {
                        $this->cache->set($file->uuid, $start, $end, $body->__toString());
                    }
                } else {
                    $this->response->getBody()->write($data);
                    $length = strlen($data);
                }
            } else {
                $stream = $fs->getBaseFilesystem()->readStream($file->getFilePathAttribute());
                $data = stream_get_contents($stream, $end - $start + 1, $start);
                $this->response->getBody()->write($data);
                $length = strlen($data);
            }

            return $this->response
                ->withStatus(206, 'Partial Content')
                ->withHeader('Accept-Ranges', 'bytes')
                ->withHeader('Content-Type', $file->type)
                ->withHeader('Content-Range', "bytes $start-$end/$file->size")
                ->withHeader('Content-Length', $length)
                ->withHeader(
                    'Access-Control-Allow-Origin',
                    getenv('CORS') ? $this->request->getHeaderLine('HTTP_ORIGIN') : ''
                );
        } catch (Throwable $e) {
            Log::error($e);
        }

        return $this->failure('Range Not Satisfiable', 416);
    }

    protected function download(File $file, ?string $title = null): ResponseInterface
    {
        $fs = $file->getFilesystem();
        try {
            if (getenv('DOWNLOAD_MEDIA_FROM_S3') && method_exists($fs, 'getDownloadLink')) {
                $link = $fs->getDownloadLink($file->getFilePathAttribute(), $title ?: $file->file);

                return $this->response
                    ->withStatus(302)
                    ->withHeader('Location', $link);
            }
        } catch (Throwable $e) {
            Log::error($e);
        }

        $stream = new Stream($fs->getBaseFilesystem()->readStream($file->getFilePathAttribute()));

        return $this->response
            ->withBody($stream)
            ->withHeader('Content-Type', $file->type)
            ->withHeader('Content-Length', $file->size)
            ->withHeader('Content-Disposition', 'attachment; filename=' . $title ?: $file->file);
    }
}