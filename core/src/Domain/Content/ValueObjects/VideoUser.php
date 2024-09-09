<?php
namespace App\Domain\Content\ValueObjects;

use Carbon\Carbon;

class VideoUser
{
    private string $videoId;
    private int $userId;
    private int $quality;
    private int $time;
    private float $speed;
    private float $volume;
    private Carbon $createdAt;
    private Carbon $updatedAt;

    public function __construct(
        string $videoId,
        int $userId,
        int $quality,
        int $time,
        float $speed,
        float $volume
    ) {
        $this->videoId = $videoId;
        $this->userId = $userId;
        $this->quality = $quality;
        $this->time = $time;
        $this->speed = $speed;
        $this->volume = $volume;
        $this->createdAt = new Carbon();
        $this->updatedAt = new Carbon();
    }

    public function updateTime(int $time): void
    {
        $this->time = $time;
        $this->updatedAt = new Carbon();
    }

    public function changeQuality(int $quality): void
    {
        $this->quality = $quality;
        $this->updatedAt = new Carbon();
    }
}