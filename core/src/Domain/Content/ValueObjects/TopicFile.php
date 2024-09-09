<?php
namespace App\Domain\Content\ValueObjects;

class TopicFile
{
    private int $topicId;
    private int $fileId;
    private string $type;

    public function __construct(int $topicId, int $fileId, string $type)
    {
        $this->topicId = $topicId;
        $this->fileId = $fileId;
        $this->type = $type;
    }
}