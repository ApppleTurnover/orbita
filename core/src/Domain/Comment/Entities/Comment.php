<?php
namespace App\Domain\Comment\Entities;

use Carbon\Carbon;

class Comment
{

    private int $id;
    private int $userId;
    private int $topicId;
    private ?int $parentId;
    private array $content;
    private bool $active;
    private int $reactionsCount;
    private Carbon $createdAt;
    private Carbon $updatedAt;
    private array $childComments = [];



    public function __construct(
        int $userId,
        int $topicId,
        array $content,
        bool $active = true,
        ?int $parentId = null
    ) {
        $this->userId = $userId;
        $this->topicId = $topicId;
        $this->content = $content;
        $this->active = $active;
        $this->parentId = $parentId;
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
        $this->reactionsCount = 0;
    }
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

    public function incrementReactions(): void
    {
        $this->reactionsCount++;
        $this->updatedAt = Carbon::now();
    }

    public function decrementReactions(): void
    {
        if ($this->reactionsCount > 0) {
            $this->reactionsCount--;
            $this->updatedAt = Carbon::now();
        }
    }

    public function addChildComment(Comment $comment): void
    {
        if ($comment->parentId !== $this->id) {
            throw new \InvalidArgumentException("This comment is not a child of the current comment.");
        }
        $this->childComments[] = $comment;
    }

    public function getChildComments(): array
    {
        return $this->childComments;
    }

    public function getLink(): string
    {
        return '/topic/' . $this->topicId . '/comment/' . $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTopicId(): int
    {
        return $this->topicId;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}