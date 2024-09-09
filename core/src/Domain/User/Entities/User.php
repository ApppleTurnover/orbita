<?php

namespace App\Domain\User\Entities;

use Carbon\Carbon;

class User
{
    private int $id;
    private int $roleId;
    private ?int $avatarId;
    private string $username;
    private string $password;
    private ?string $fullname;
    private ?string $email;
    private ?string $phone;
    private bool $active;
    private bool $blocked;
    private bool $notify;
    private ?string $lang;
    private ?string $resetPassword;
    private ?Carbon $resetAt;
    private Carbon $createdAt;
    private Carbon $updatedAt;
    private ?Carbon $activeAt;

    public function __construct(
        int $id,
        int $roleId,
        string $username,
        string $password,
        ?string $email,
        ?string $fullname = null,
        ?string $phone = null
    ) {
        $this->id = $id;
        $this->roleId = $roleId;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->fullname = $fullname;
        $this->phone = $phone;
        $this->active = false;
        $this->blocked = false;
        $this->notify = true;
        $this->lang = 'en';
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function updateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }
        $this->email = $email;
    }
}