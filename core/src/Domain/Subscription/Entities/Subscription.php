<?php
namespace App\Domain\Subscription\Entities;

use App\Domain\Payment\Entities\Payment;
use Carbon\Carbon;

class Subscription
{
    private int $id;
    private int $userId;
    private int $levelId;
    private ?int $nextLevelId;
    private string $service;
    private int $period;
    private ?int $nextPeriod;
    private bool $active;
    private bool $cancelled;
    private ?string $remoteId;
    private Carbon $createdAt;
    private Carbon $updatedAt;
    private Carbon $activeUntil;

    public function __construct(
        int $userId,
        int $levelId,
        string $service,
        int $period,
        ?Carbon $activeUntil = null,
        bool $active = false,
        bool $cancelled = false
    ) {
        $this->userId = $userId;
        $this->levelId = $levelId;
        $this->service = $service;
        $this->period = $period;
        $this->active = $active;
        $this->cancelled = $cancelled;
        $this->activeUntil = $activeUntil ?? Carbon::now()->addMonths($period);
    }

    public function activate(?int $nextLevelId = null, ?int $nextPeriod = null): void
    {
        $now = Carbon::now();

        if (!$this->activeUntil || $this->activeUntil < $now || $nextLevelId) {
            $this->activeUntil = $now->addMonths($nextPeriod ?? $this->period);
            $this->levelId = $nextLevelId ?? $this->levelId;
        } else {
            $this->activeUntil = $this->activeUntil->addMonths($nextPeriod ?? $this->period);
        }

        $this->active = true;
        $this->cancelled = false;
        $this->nextLevelId = null;
        $this->nextPeriod = null;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function paidAmountLeft(): float
    {
        $daysLeft = Carbon::now()->diffInDays($this->activeUntil);
        if ($daysLeft < 1) {
            return 0;
        }

        return $this->level->costPerDay() * $daysLeft;
    }

    public function charge(): bool
    {
        $service = $this->getService();
        if ($this->remoteId && $service->canProcessSubscription()) {
            $payment = $this->createPayment($this->nextPeriod ?? $this->period);
            return $service->charge($payment);
        }

        return false;
    }

    public function createPayment(int $period): Payment
    {
        return new Payment($this->userId, $this->id, $this->amountForPeriod($period), $period);
    }

    public function amountForPeriod(int $period): float
    {
        return $this->level->price * $period;
    }
}