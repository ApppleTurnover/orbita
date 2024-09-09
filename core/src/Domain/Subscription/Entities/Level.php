<?php
namespace App\Domain\Subscription\Entities;

use Carbon\Carbon;

class Level
{
    private int $id;
    private string $title;
    private ?string $content;
    private float $price;
    private ?int $coverId;
    private bool $active;
    private Carbon $createdAt;
    private Carbon $updatedAt;

    public function __construct(int $id, string $title, float $price, ?int $coverId = null, bool $active = true, ?string $content = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->coverId = $coverId;
        $this->active = $active;
        $this->content = $content;
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

    public function costPerDay(): float
    {
        $cost = round($this->price / 30, 2);
        return $cost > 1 ? round($cost) : $cost;
    }

    public function costForPeriod(int $period): float
    {
        return $this->price * $period;
    }
}