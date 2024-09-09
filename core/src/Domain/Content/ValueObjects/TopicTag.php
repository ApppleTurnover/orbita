<?php
namespace App\Domain\Content\ValueObjects;

class TopicTag
{
    private int $topicId;
    private int $tagId;

    public function __construct(int $topicId, int $tagId)
    {
        $this->topicId = $topicId;
        $this->tagId = $tagId;
    }
}