<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\PostUpdateResultInterface;
use WP_Post;

final class PostUpdateResultDto implements PostUpdateResultInterface
{
    public function __construct(
        private readonly bool $updated,
        private readonly ?WP_Post $post,
        private readonly ?string $error = null
    ) {}

    public function isUpdated(): bool
    {
        return $this->updated;
    }

    public function getPost(): ?WP_Post
    {
        return $this->post;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public static function success(WP_Post $post): self
    {
        return new self(
            updated: true,
            post: null,
            error: null
        );
    }

    public static function error(string $error): self
    {
        return new self(
            updated: false,
            post: null,
            error: $error
        );
    }
}
