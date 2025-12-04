<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\PostCreateResultInterface;
use WP_Post;

final class PostCreateResultDto implements PostCreateResultInterface
{
    public function __construct(
        private readonly bool $created,
        private readonly ?WP_Post $post,
        private readonly ?string $error = null,
        private readonly bool $duplicate = false
    ) {}

    public function isCreated(): bool
    {
        return $this->created;
    }

    public function getPost(): ?WP_Post
    {
        return $this->post;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function isDuplicate(): bool
    {
        return $this->duplicate;
    }

    // Static factory methods
    public static function success(WP_Post $post): self
    {
        return new self(true, $post);
    }

    public static function error(string $error, bool $duplicate = false): self
    {
        return new self(
            created: false,
            post: null,
            error: $error,
            duplicate: $duplicate
        );
    }

    public static function duplicate(): self
    {
        return new self(
            created: false,
            post: null,
            error: 'Duplicate post found',
            duplicate: true
        );
    }
}
