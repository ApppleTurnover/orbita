<?php

namespace App\Domain\Content\Entities;

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

    public function __construct(string $id, string $title, string $description, int $duration, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->duration = $duration;
        $this->active = $active;
        $this->createdAt = new Carbon();
        $this->updatedAt = new Carbon();
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }
}