<?php

namespace App\Domain\Reaction\ValueObjects;

use Carbon\Carbon;

class TopicReaction
{
    private int $topicId;
    private int $reactionId;
    private int $userId;
    private Carbon $timestamp;

    public function __construct(int $topicId, int $reactionId, int $userId)
    {
        $this->topicId = $topicId;
        $this->reactionId = $reactionId;
        $this->userId = $userId;
        $this->timestamp = Carbon::now();
    }

    public function updateTimestamp(): void
    {
        $this->timestamp = Carbon::now();
    }

    public function getTopicId(): int
    {
        return $this->topicId;
    }

    public function getReactionId(): int
    {
        return $this->reactionId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTimestamp(): Carbon
    {
        return $this->timestamp;
    }
}