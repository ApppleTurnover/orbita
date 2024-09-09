<?php
namespace App\Domain\File\Entities;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class File
{
    private string $uuid;
    private bool $temporary;
    private array $metadata;
    private Carbon $createdAt;
    private Carbon $updatedAt;

    public function __construct(bool $temporary = false, array $metadata = [])
    {
        $this->uuid = Uuid::uuid4();
        $this->temporary = $temporary;
        $this->metadata = $metadata;
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
    }

    public function markAsPermanent(): void
    {
        $this->temporary = false;
        $this->updatedAt = Carbon::now();
    }

    public function updateMetadata(array $newMetadata): void
    {
        $this->metadata = $newMetadata;
        $this->updatedAt = Carbon::now();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function isTemporary(): bool
    {
        return $this->temporary;
    }
}