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

    public function hasAccess(User $user): bool
    {
        if ($this->isFree()) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($this->levelId && $user->hasSubscription($this->levelId)) {
            return true;
        }

        return false;
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
            if (!$user->wantsNotification()) {
                continue;
            }

            $user->notify('topic-new', $this->id);
        }
    }
}