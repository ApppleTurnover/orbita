<?php
namespace App\Domain\User\Entities;

use Carbon\Carbon;

class UserNotification
{
    private string $id;
    private int $userId;
    private int $topicId;
    private ?int $commentId;
    private string $type;
    private bool $active;
    private bool $sent;
    private Carbon $createdAt;
    private ?Carbon $sentAt;

    public function __construct(
        int $userId,
        int $topicId,
        string $type,
        ?int $commentId = null
    ) {
        $this->id = uniqid(); // Генерация уникального ID
        $this->userId = $userId;
        $this->topicId = $topicId;
        $this->type = $type;
        $this->commentId = $commentId;
        $this->active = true;
        $this->sent = false;
        $this->createdAt = Carbon::now();
        $this->sentAt = null;
    }

    public function markAsSent(): void
    {
        $this->sent = true;
        $this->sentAt = Carbon::now();
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function getType(): string
    {
        return $this->type;
    }
}