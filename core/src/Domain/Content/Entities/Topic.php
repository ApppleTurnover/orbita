<?php
namespace App\Domain\Content\Entities;

use App\Domain\Payment\Entities\Payment;
use App\Domain\User\Entities\User;
use Carbon\Carbon;

class Topic
{
    private int $id;
    private string $uuid;
    private string $title;
    private array $content;
    private ?string $teaser;
    private int $userId;
    private ?int $coverId;
    private ?int $levelId;
    private ?float $price;
    private bool $active;
    private bool $closed;
    private int $commentsCount;
    private int $viewsCount;
    private int $reactionsCount;
    private ?int $lastCommentId;
    private Carbon $createdAt;
    private Carbon $updatedAt;
    private ?Carbon $publishedAt;
    private ?Carbon $publishAt;

    public function __construct(
        string $uuid,
        string $title,
        array $content,
        int $userId,
        ?float $price = null,
        ?int $levelId = null,
        bool $active = true,
        bool $closed = false,
        ?string $teaser = null
    ) {
        $this->uuid = $uuid;
        $this->title = $title;
        $this->content = $content;
        $this->userId = $userId;
        $this->price = $price;
        $this->levelId = $levelId;
        $this->active = $active;
        $this->closed = $closed;
        $this->teaser = $teaser;
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
        $this->viewsCount = 0;
        $this->commentsCount = 0;
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

    public function close(): void
    {
        $this->closed = true;
        $this->updatedAt = Carbon::now();
    }

    public function open(): void
    {
        $this->closed = false;
        $this->updatedAt = Carbon::now();
    }

    public function updateContent(array $newContent): void
    {
        $this->content = $newContent;
        $this->updatedAt = Carbon::now();
    }

    public function isFree(): bool
    {
        return $this->price === null && $this->levelId === null;
    }

    public function incrementViews(): void
    {
        $this->viewsCount++;
        $this->updatedAt = Carbon::now();
    }

    public function incrementCommentsCount(): void
    {
        $this->commentsCount++;
        $this->updatedAt = Carbon::now();
    }

    public function incrementReactionsCount(): void
    {
        $this->reactionsCount++;
        $this->updatedAt = Carbon::now();
    }

    public function createPayment(User $user, string $serviceName): Payment
    {
        if ($this->hasAccess($user)) {
            throw new \LogicException('User already has access');
        }

        return new Payment($user->getId(), $this->id, $serviceName, $this->price);
    }

    public function createNotifications(array $users): void
    {
        foreach ($users as $user) {
            if ($user->wantsNotification()) {
                $user->notify('topic-new', $this->id);
            }
        }
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function hasAccess(User $user)
    {
    }
}