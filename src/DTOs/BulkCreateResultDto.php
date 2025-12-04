<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use WP_Post;

final class BulkCreateResultDto
{
    /**
     * @param array<array{index: int, post: WP_Post, title: string}> $success
     * @param array<array{index: int, title: string, error: string}> $failed
     */
    public function __construct(
        private readonly array $success,
        private readonly array $failed,
        private readonly int $total,
        private readonly int $created
    ) {}

    public function getSuccess(): array
    {
        return $this->success;
    }

    public function getFailed(): array
    {
        return $this->failed;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getSuccessCount(): int
    {
        return count($this->success);
    }

    public function getFailedCount(): int
    {
        return count($this->failed);
    }

    public function getSuccessRate(): float
    {
        return $this->total > 0 ? ($this->created / $this->total) * 100 : 0;
    }
}
