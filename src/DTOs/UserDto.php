<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\Able\ArrayAbleInterface;
use Vigihdev\WpCliMake\Contracts\UserInterface;

final class UserDto implements UserInterface, ArrayAbleInterface
{

    public function __construct(
        private readonly string $password,
        private readonly string $username,
        private readonly string $email,
        private readonly string $role,
        private readonly ?string $displayName = null,
        private readonly ?string $nickname = null,
        private readonly ?string $firstName = null,
        private readonly ?string $lastName = null,
        private readonly ?string $description = null,
    ) {}

    public function password(): string
    {
        return $this->password;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function displayName(): ?string
    {
        return $this->displayName;
    }

    public function nickname(): ?string
    {
        return $this->nickname;
    }

    public function firstName(): ?string
    {
        return $this->firstName;
    }

    public function lastName(): ?string
    {
        return $this->lastName;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return array_filter([
            'user_pass' => $this->password(),
            'user_login' => $this->username(),
            'user_nicename' => $this->nickname() ?? $this->username(),
            'user_email' => $this->email(),
            'display_name' => $this->displayName() ?? $this->username(),
            'nickname' => $this->nickname() ?? $this->username(),
            'first_name' => $this->firstName(),
            'last_name' => $this->lastName(),
            'description' => $this->description(),
            'role' => $this->role(),
        ], function ($value) {
            return $value !== null;
        });
    }
}
