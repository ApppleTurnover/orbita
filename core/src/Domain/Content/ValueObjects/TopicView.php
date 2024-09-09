<?php
namespace App\Domain\Content\ValueObjects;

class
TopicView
{
    private int $topicId;
    private int $userId;
    private \DateTime $timestamp;

    public function __construct(int $topicId, int $userId, \DateTime $timestamp)
    {
        $this->topicId = $topicId;
        $this->userId = $userId;
        $this->timestamp = $timestamp;
    }

    public function updateTimestamp(): void
    {
        $this->timestamp = new \DateTime();
    }
}