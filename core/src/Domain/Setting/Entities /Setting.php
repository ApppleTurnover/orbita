<?php

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
}