<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Factory;

use WP_Post;
use Vigihdev\WpCliMake\DTOs\{PostDataResultDto, PostCreateResultDto, PostUpdateResultDto, BulkCreateResultDto};
use Vigihdev\WpCliMake\Contracts\{PostDataResultInterface, PostCreateResultInterface, PostUpdateResultInterface};
use WP_User;

final class PostFactory
{
    /**
     * Create a new post
     *
     * @return PostCreateResultInterface
     */
    public static function create(string $title, array $args = []): PostCreateResultInterface
    {
        // Check for duplicate title
        $is_duplicate = self::isDuplicate($title, $args['type'] ?? 'post');

        if ($is_duplicate) {
            return PostCreateResultDto::duplicate();
        }

        // If unique_title flag is set, modify title
        if ($args['unique_title'] ?? false) {
            $title = self::makeUniqueTitle($title);
        }

        // Prepare and validate post data
        $post_data = self::preparePostData($title, $args);
        $validation = self::validatePostData($post_data);

        if (!$validation->isValid()) {
            return PostCreateResultDto::error($validation->getMessage());
        }

        // Insert post
        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            return PostCreateResultDto::error($post_id->get_error_message());
        }

        // Handle additional data
        self::handlePostMeta($post_id, $args['meta'] ?? []);
        self::handlePostTerms($post_id, $args);

        $post = get_post($post_id);
        return $post ? PostCreateResultDto::success($post) : PostCreateResultDto::error('Failed to retrieve created post');
    }

    /**
     * Update existing post
     */
    public static function update(int $post_id, array $args): PostUpdateResultInterface
    {
        // Verify post exists
        if (!self::find($post_id)) {
            return PostUpdateResultDto::error("Post with ID {$post_id} not found");
        }

        $args['ID'] = $post_id;
        $post_id = wp_update_post($args, true);

        if (is_wp_error($post_id)) {
            return PostUpdateResultDto::error($post_id->get_error_message());
        }

        // Handle meta if provided
        if (isset($args['meta']) && is_array($args['meta'])) {
            self::handlePostMeta($post_id, $args['meta']);
        }

        // Handle terms if provided
        self::handlePostTerms($post_id, $args);

        $post = get_post($post_id);
        return $post ? PostUpdateResultDto::success($post) : PostUpdateResultDto::error('Failed to retrieve updated post');
    }

    /**
     * Validate post data
     */
    private static function validatePostData(array $post_data): PostDataResultInterface
    {
        $errors = [];

        // Check required fields
        if (empty($post_data['post_title'])) {
            $errors[] = 'Post title is required';
        }

        if (empty($post_data['post_author'])) {
            $errors[] = 'Valid author ID is required';
        }

        // Check title length
        if (isset($post_data['post_title']) && strlen($post_data['post_title']) > 255) {
            $errors[] = 'Post title is too long (max 255 characters)';
        }

        // Check author exists
        if (isset($post_data['post_author'])) {
            $author = get_userdata((int) $post_data['post_author']);
            if (!$author) {
                $errors[] = 'Author does not exist';
            }
        }

        // Check post type is valid
        if (isset($post_data['post_type'])) {
            $post_types = get_post_types(['public' => true]);
            if (!in_array($post_data['post_type'], $post_types, true)) {
                $errors[] = 'Invalid post type';
            }
        }

        if (!empty($errors)) {
            return PostDataResultDto::error('Validation failed', $errors);
        }

        return PostDataResultDto::success();
    }

    /**
     * Get post by ID with validation
     */
    public static function find(int $post_id): ?WP_Post
    {
        $post = get_post($post_id);
        return ($post instanceof WP_Post) ? $post : null;
    }

    /**
     * Bulk create posts
     */
    public static function bulkCreate(array $posts_data): BulkCreateResultDto
    {
        $success = [];
        $failed = [];
        $created = 0;

        foreach ($posts_data as $index => $post_data) {
            $title = $post_data['title'] ?? 'Untitled Post ' . ($index + 1);
            $args = $post_data['args'] ?? [];

            $result = self::create($title, $args);

            if ($result->isCreated() && $result->getPost()) {
                $success[] = [
                    'index' => $index,
                    'post' => $result->getPost(),
                    'title' => $title
                ];
                $created++;
            } else {
                $failed[] = [
                    'index' => $index,
                    'title' => $title,
                    'error' => $result->getError() ?? 'Unknown error'
                ];
            }
        }

        return new BulkCreateResultDto(
            success: $success,
            failed: $failed,
            total: count($posts_data),
            created: $created
        );
    }

    private static function findOneAuthor(): ?WP_User
    {
        $users = get_users([
            'number'  => 1,
            'orderby' => 'ID',
            'order'   => 'ASC'
        ]);

        return !empty($users) ? $users[0] : null;
    }

    /**
     * Prepare post data array (sama seperti sebelumnya)
     */
    private static function preparePostData(string $title, array $args): array
    {
        $authorId = get_current_user_id() > 0 ? get_current_user_id() : (int)self::findOneAuthor()?->ID;

        $post_data = [
            'post_title'   => sanitize_text_field($title),
            'post_content' => wp_kses_post($args['content'] ?? ''),
            'post_status'  => self::validateStatus($args['status'] ?? 'draft'),
            'post_type'    => self::validateType($args['type'] ?? 'post'),
            'post_author'  => self::validateAuthor($args['author'] ?? $authorId),
            'post_excerpt' => wp_kses_post($args['excerpt'] ?? ''),
            'post_date'    => self::validateDate($args['date'] ?? current_time('mysql')),
            'post_name'    => sanitize_title($args['slug'] ?? $title),
            'post_parent'  => absint($args['parent'] ?? 0),
            'comment_status' => $args['comment_status'] ?? get_option('default_comment_status', 'open'),
            'ping_status'    => $args['ping_status'] ?? get_option('default_ping_status', 'open'),
        ];


        // Remove empty values
        return array_filter($post_data, function ($value) {
            return $value !== '' && $value !== null;
        });
    }

    // ... (methods lainnya seperti isDuplicate, getCategoryId, dll tetap sama)
    private static function isDuplicate(string $title, string $post_type = 'post'): bool
    {
        global $wpdb;

        $clean_title = self::normalizeTitle($title);
        $slug = sanitize_title($title);

        $sql = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} 
             WHERE (post_title = %s OR post_name = %s)
             AND post_type = %s
             AND post_status NOT IN ('trash', 'auto-draft')
             LIMIT 1",
            $clean_title,
            $slug,
            $post_type
        );

        return (bool) $wpdb->get_var($sql);
    }

    private static function getCategoryId($category): ?int
    {
        if (empty($category)) {
            return null;
        }

        if (is_numeric($category)) {
            $term = get_term(absint($category), 'category');
        } else {
            $term = get_term_by('name', sanitize_text_field($category), 'category');
        }

        return ($term && !is_wp_error($term)) ? $term->term_id : null;
    }

    private static function handlePostMeta(int $post_id, array $meta): void
    {
        foreach ($meta as $key => $value) {
            if (is_string($key) && !empty($key)) {
                update_post_meta($post_id, sanitize_key($key), $value);
            }
        }
    }

    private static function handlePostTerms(int $post_id, array $args): void
    {
        // Handle category
        if (!empty($args['category'])) {
            $category_id = self::getCategoryId($args['category']);
            if ($category_id) {
                wp_set_post_categories($post_id, [$category_id]);
            }
        }
    }

    private static function normalizeTitle(string $title): string
    {
        $title = trim($title);
        $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $title = preg_replace('/\s+/', ' ', $title);
        return $title;
    }

    private static function makeUniqueTitle(string $title): string
    {
        $timestamp = current_time('Ymd-His');
        return $title . ' - ' . $timestamp;
    }

    private static function validateStatus(string $status): string
    {
        $valid_statuses = ['draft', 'publish', 'pending', 'future', 'private'];
        return in_array($status, $valid_statuses, true) ? $status : 'draft';
    }

    private static function validateType(string $type): string
    {
        $valid_types = get_post_types(['public' => true]);
        return in_array($type, $valid_types, true) ? $type : 'post';
    }

    private static function validateAuthor(int $author_id): int
    {
        $user = get_userdata($author_id);
        return $user ? $author_id : get_current_user_id();
    }

    private static function validateDate(string $date): string
    {
        $timestamp = strtotime($date);
        return $timestamp !== false ? date('Y-m-d H:i:s', $timestamp) : current_time('mysql');
    }
}
