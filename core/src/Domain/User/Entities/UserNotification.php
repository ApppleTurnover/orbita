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
        string $id,
        int $userId,
        int $topicId,
        string $type,
        bool $active = true,
        bool $sent = false,
        ?Carbon $sentAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->topicId = $topicId;
        $this->type = $type;
        $this->active = $active;
        $this->sent = $sent;
        $this->createdAt = new Carbon();
        $this->sentAt = $sentAt;
    }

    public function markAsSent(): void
    {
        $this->sent = true;
        $this->sentAt = Carbon::now();
    }
}