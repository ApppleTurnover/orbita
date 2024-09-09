<?php
namespace App\Domain\Reaction\ValueObjects;

use Carbon\Carbon;

class CommentReaction
{
    private int $commentId;
    private int $reactionId;
    private int $userId;
    private Carbon $timestamp;

    public function __construct(int $commentId, int $reactionId, int $userId)
    {
        $this->commentId = $commentId;
        $this->reactionId = $reactionId;
        $this->userId = $userId;
        $this->timestamp = Carbon::now();
    }

    public function updateTimestamp(): void
    {
        $this->timestamp = Carbon::now();
    }

    public function getCommentId(): int
    {
        return $this->commentId;
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