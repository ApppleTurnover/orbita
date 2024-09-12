<?php

namespace App\Domain\Content\Entities;

class Tag
{
    private int $id;
    private string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function rename(string $newTitle): void
    {
        $this->title = $newTitle;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isSameTag(string $tagTitle): bool
    {
        return strtolower($this->title) === strtolower($tagTitle);
    }
}