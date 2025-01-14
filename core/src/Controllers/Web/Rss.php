<?php

namespace App\Controllers\Web;

use App\Models\Topic;
use App\Services\Utils;
use Psr\Http\Message\ResponseInterface;

class Rss extends Sitemap
{
    public function get(): ResponseInterface
    {
        $rows = [];
        $topics = Topic::query()
            ->where('active', true)
            ->select('uuid', 'title', 'content', 'teaser', 'cover_id', 'user_id', 'published_at')
            ->with('cover:id,uuid,type,updated_at')
            ->with('user:id,fullname')
            ->limit($this->getProperty('limit', 20))
            ->orderByDesc('published_at');
        foreach ($topics->get() as $topic) {
            /** @var Topic $topic */
            $row = [
                'link' => $topic->getLink(),
                'title' => $topic->title,
                'description' => $topic->teaser,
                'content' => '',
                'date' => $topic->published_at->toIso8601String(),
                'author' => [
                    ['name' => $topic->user->fullname],
                ],
            ];
            if ($topic->cover) {
                $row['image'] = [
                    'type' => $topic->cover->type,
                    'url' => Utils::getImageLink($topic->cover->only('id', 'uuid', 'updated_at')),
                ];
            }
            if ($topic->hasAccess()) {
                $row['content'] = Utils::renderContent($topic->content['blocks']);
            }
            $rows[] = $row;
        }

        return $this->success($rows);
    }
}