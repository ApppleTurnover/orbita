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
        int $roleId,
        string $username,
        string $password,
        ?string $email,
        ?string $fullname = null,
        ?string $phone = null
    ) {
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
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
        $this->resetAt = null;
        $this->activeAt = null;
    }

    public function activate(): void
    {
        $this->active = true;
        $this->activeAt = Carbon::now();
        $this->updatedAt = Carbon::now();
    }

    public function deactivate(): void
    {
        $this->active = false;
        $this->updatedAt = Carbon::now();
    }

    public function block(): void
    {
        $this->blocked = true;
        $this->updatedAt = Carbon::now();
    }

    public function unblock(): void
    {
        $this->blocked = false;
        $this->updatedAt = Carbon::now();
    }

    public function updateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }
        $this->email = $email;
        $this->updatedAt = Carbon::now();
    }

    public function resetPassword(string $newPassword): void
    {
        $this->resetPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->resetAt = Carbon::now();
    }

    public function toggleNotifications(): void
    {
        $this->notify = !$this->notify;
        $this->updatedAt = Carbon::now();
    }

    public function isAdmin(): bool
    {
        return $this->roleId === 1;
    }

    public function getLanguage(): string
    {
        return $this->lang;
    }

    public function setLanguage(string $lang): void
    {
        $this->lang = $lang;
        $this->updatedAt = Carbon::now();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }
}