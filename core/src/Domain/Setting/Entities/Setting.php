<?php
namespace App\Domain\Setting\Entities;

class Setting
{
    private string $key;
    private ?string $value;
    private string $type;
    private bool $required;
    private int $rank;

    public function __construct(string $key, string $type, bool $required, int $rank, ?string $value = null)
    {
        $this->key = $key;
        $this->type = $type;
        $this->required = $required;
        $this->rank = $rank;
        $this->value = $value;
    }

    public function updateValue(?string $newValue): void
    {
        if ($this->required && $newValue === null) {
            throw new \InvalidArgumentException('This setting is required and cannot be null.');
        }
        $this->value = $newValue;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRank(): int
    {
        return $this->rank;
    }
}