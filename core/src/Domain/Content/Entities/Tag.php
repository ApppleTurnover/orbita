<?php
namespace App\Domain\Content\Entities;

class Tag
{
    private int $id;
    private string $title;

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function rename(string $newTitle): void
    {
        $this->title = $newTitle;
    }
}