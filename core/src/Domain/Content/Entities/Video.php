<?php

namespace App\Domain\Content\Entities;

use App\Domain\Content\ValueObjects\PageFile;
use App\Domain\Content\ValueObjects\TopicFile;
use App\Domain\User\Entities\User;
use Carbon\Carbon;

class Video
{
    private string $id;
    private string $title;
    private string $description;
    private int $duration;
    private bool $active;
    private Carbon $createdAt;
    private Carbon $updatedAt;

    /** @var PageFile[] */
    private array $pageFiles = [];
    /** @var TopicFile[] */
    private array $topicFiles = [];

    public function __construct(
        string $id,
        string $title,
        string $description,
        int $duration,
        bool $active = true,
        array $pageFiles = [],
        array $topicFiles = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->duration = $duration;
        $this->active = $active;
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
        $this->pageFiles = $pageFiles;
        $this->topicFiles = $topicFiles;
    }

    public function activate(): void
    {
        $this->active = true;
        $this->updatedAt = Carbon::now();
    }

    public function deactivate(): void
    {
        $this->active = false;
        $this->updatedAt = Carbon::now();
    }

    public function updateTitle(string $newTitle): void
    {
        $this->title = $newTitle;
        $this->updatedAt = Carbon::now();
    }

    public function updateDescription(string $newDescription): void
    {
        $this->description = $newDescription;
        $this->updatedAt = Carbon::now();
    }

    public function updateDuration(int $newDuration): void
    {
        $this->duration = $newDuration;
        $this->updatedAt = Carbon::now();
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function hasPageFiles(string $type): bool
    {
        foreach ($this->pageFiles as $pageFile) {
            if ($pageFile->getType() === $type) {
                return true;
            }
        }
        return false;
    }

    public function hasTopicFilesWithAccess(string $type, User $user): bool
    {
        foreach ($this->topicFiles as $topicFile) {
            if ($topicFile->getType() === $type && $topicFile->hasAccess($user)) {
                return true;
            }
        }
        return false;
    }
}