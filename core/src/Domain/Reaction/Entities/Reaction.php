<?php
namespace App\Domain\Reaction\Entities;

use App\Domain\Reaction\ValueObjects\CommentReaction;
use App\Domain\Reaction\ValueObjects\TopicReaction;

class Reaction
{
    private int $id;
    private string $title;
    private string $emoji;
    private int $rank;
    private bool $active;
    private array $topicReactions = [];
    private array $commentReactions = [];

    public function __construct(string $title, string $emoji, int $rank = 0, bool $active = true)
    {
        $this->title = $title;
        $this->emoji = $emoji;
        $this->rank = $rank;
        $this->active = $active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function updateRank(int $newRank): void
    {
        $this->rank = $newRank;
    }

    public function addTopicReaction(TopicReaction $reaction): void
    {
        $this->topicReactions[] = $reaction;
    }

    public function addCommentReaction(CommentReaction $reaction): void
    {
        $this->commentReactions[] = $reaction;
    }

    public function getReactionsCount(): int
    {
        return count($this->topicReactions) + count($this->commentReactions);
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getEmoji(): string
    {
        return $this->emoji;
    }

    public function getRank(): int
    {
        return $this->rank;
    }
}