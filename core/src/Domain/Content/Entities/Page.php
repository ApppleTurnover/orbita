<?php

namespace App\Domain\Content\Entities;

use Carbon\Carbon;

class Page
{
    private int $id;
    private string $name;
    private ?string $title;
    private bool $external;
    private ?array $content;
    private ?string $alias;
    private ?string $position;
    private ?string $link;
    private bool $blank;
    private int $rank;
    private bool $active;
    private Carbon $createdAt;
    private Carbon $updatedAt;

    public function __construct(
        string $name,
        ?string $title = null,
        bool $external = false,
        ?array $content = null,
        ?string $alias = null,
        ?string $position = null,
        ?string $link = null,
        bool $blank = false,
        int $rank = 0,
        bool $active = true
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->external = $external;
        $this->content = $content;
        $this->alias = $alias;
        $this->position = $position;
        $this->link = $link;
        $this->blank = $blank;
        $this->rank = $rank;
        $this->active = $active;
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
    }

    public function updateContent(?array $newContent): void
    {
        $this->content = $newContent;
        $this->updatedAt = Carbon::now();
    }

    // Активация страницы
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getFullLink(): ?string
    {
        return $this->link ?: ($this->alias ? '/'.$this->alias : null);
    }

    public function updateTitle(?string $newTitle): void
    {
        $this->title = $newTitle;
        $this->updatedAt = Carbon::now();
    }

    public function isExternal(): bool
    {
        return $this->external;
    }
}