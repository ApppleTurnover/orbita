<?php
namespace App\Domain\Payment\Entities;

use App\Services\PaymentService;
use Carbon\Carbon;

class Payment
{
    private string $id;
    private int $userId;
    private ?int $subscriptionId;
    private ?int $topicId;
    private string $service;
    private float $amount;
    private ?bool $paid;
    private ?Carbon $paidAt;
    private array $metadata;
    private string $remoteId;
    private string $link;

    public function __construct(
        string $id,
        int $userId,
        string $service,
        float $amount,
        array $metadata = [],
        ?int $subscriptionId = null,
        ?int $topicId = null,
        ?bool $paid = null,
        ?Carbon $paidAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->subscriptionId = $subscriptionId;
        $this->topicId = $topicId;
        $this->service = $service;
        $this->amount = $amount;
        $this->paid = $paid;
        $this->paidAt = $paidAt;
        $this->metadata = $metadata;
    }

    public function markAsPaid(): void
    {
        $this->paid = true;
        $this->paidAt = Carbon::now();
    }

    public function markAsUnpaid(): void
    {
        $this->paid = false;
        $this->paidAt = null;
    }

    public function checkStatus(PaymentService $paymentService): bool
    {
        $status = $paymentService->getPaymentStatus($this);
        if ($status) {
            $this->markAsPaid();
        } else {
            $this->markAsUnpaid();
        }

        return $this->paid;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSubscriptionId(): ?int
    {
        return $this->subscriptionId;
    }

    public function getTopicId(): ?int
    {
        return $this->topicId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getRemoteId(): string {
        return $this->remoteId;
    }

    public function getLink(): string {
        return $this->link;
    }

    public function setRemoteId(string $remoteId): void
    {
        $this->remoteId = $remoteId;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }
}