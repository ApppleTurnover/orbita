<?php
namespace App\Domain\Content\ValueObjects;

class PageFile
{
    private int $pageId;
    private int $fileId;
    private string $type;

    public function __construct(int $pageId, int $fileId, string $type)
    {
        $this->pageId = $pageId;
        $this->fileId = $fileId;
        $this->type = $type;
    }

    public function getFileType(): string
    {
        return $this->type;
    }
}