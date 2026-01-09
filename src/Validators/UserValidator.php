<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Contracts\UserInterface;
use Vigihdev\WpCliMake\Exceptions\UserException;
use Vigihdev\WpCliMake\DTOs\UserDto;

final class UserValidator
{

    public function __construct(
        private readonly UserInterface $user,
    ) {}

    public function mustUniqueUsername(): self
    {
        $username = $this->user->username();
        if (username_exists($username)) {
            throw UserException::duplicateUsername($username);
        }
        return $this;
    }

    public function mustUniqueEmail(): self
    {
        $email = $this->user->email();
        if (email_exists($email)) {
            throw UserException::duplicateEmail($email);
        }
        return $this;
    }

    public function mustBeValidPassword(): self
    {
        $password = $this->user->password();
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/', $password)) {
            throw UserException::invalidPassword($password);
        }
        return $this;
    }
    public function mustBeValidUsername(): self
    {
        $username = $this->user->username();
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw UserException::invalidUsername($username);
        }
        return $this;
    }

    public function mustBeValidEmail(): self
    {
        $email = $this->user->email();
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw UserException::invalidEmail($email);
        }
        return $this;
    }
}
