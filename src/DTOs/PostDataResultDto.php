<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\PostDataResultInterface;

final class PostDataResultDto implements PostDataResultInterface
{
    public function __construct(
        private readonly bool $status,
        private readonly string $message,
        private readonly array $errors = []
    ) {}

    public function isValid(): bool
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    // Static factory method
    public static function success(string $message = 'OK'): self
    {
        return new self(true, $message);
    }

    public static function error(string $message, array $errors = []): self
    {
        return new self(false, $message, $errors);
    }
}
