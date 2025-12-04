<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;

use WP_Post;

interface PostFactoryInterface
{
    /**
     * Create a new post
     */
    public static function create(string $title, array $args = []): array;

    /**
     * Check if post title is duplicate
     */
    public static function isDuplicate(string $title, string $post_type = 'post'): bool;

    /**
     * Get duplicate post info
     */
    public static function getDuplicateInfo(string $title, string $post_type = 'post'): ?array;

    /**
     * Update existing post
     */
    public static function update(int $post_id, array $args): PostUpdateResultInterface;

    /**
     * Find post by ID
     */
    public static function find(int $post_id): ?WP_Post;
}
