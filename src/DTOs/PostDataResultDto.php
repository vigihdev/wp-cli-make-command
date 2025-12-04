<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\PostDataResultInterface;

/**
 * Class PostDataResultDto
 *
 * DTO untuk menyimpan dan mengakses data hasil operasi post data
 */
final class PostDataResultDto implements PostDataResultInterface
{
    /**
     * Membuat instance objek PostDataResultDto dengan parameter yang ditentukan
     *
     * @param bool $status Status hasil operasi
     * @param string $message Pesan deskriptif tentang hasil operasi
     */
    public function __construct(
        private readonly bool $status,
        private readonly string $message,
        private readonly array $errors = []
    ) {}

    public function isValid(): bool
    {
        return $this->status;
    }

    /**
     * Mendapatkan status dari hasil operasi
     *
     * @return bool Status hasil operasi
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * Mendapatkan pesan dari hasil operasi
     *
     * @return string Pesan deskriptif tentang hasil operasi
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function success(string $message = 'OK'): self
    {
        return new self(
            status: true,
            message: $message
        );
    }

    public static function error(string $message, array $errors = []): self
    {
        return new self(
            status: false,
            message: $message,
            errors: $errors
        );
    }
}
