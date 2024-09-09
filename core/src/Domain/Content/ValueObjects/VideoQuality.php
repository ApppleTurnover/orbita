<?php
namespace App\Domain\Content\ValueObjects;

use Carbon\Carbon;

class VideoQuality
{
    private int $quality;
    private int $videoId;
    private int $fileId;
    private float $progress;
    private bool $processed;
    private bool $moved;
    private string $bandwidth;
    private string $resolution;
    private string $manifest;
    private Carbon $createdAt;
    private ?Carbon $processedAt;
    private ?Carbon $movedAt;

    public function __construct(
        int $quality,
        int $videoId,
        int $fileId,
        string $resolution,
        float $progress = 0,
        bool $processed = false,
        bool $moved = false,
        ?Carbon $processedAt = null,
        ?Carbon $movedAt = null
    ) {
        $this->quality = $quality;
        $this->videoId = $videoId;
        $this->fileId = $fileId;
        $this->resolution = $resolution;
        $this->progress = $progress;
        $this->processed = $processed;
        $this->moved = $moved;
        $this->processedAt = $processedAt;
        $this->movedAt = $movedAt;
        $this->createdAt = new Carbon();
    }

    public function finishProcessing(): void
    {
        $this->progress = 100;
        $this->processed = true;
        $this->processedAt = Carbon::now();
    }

}