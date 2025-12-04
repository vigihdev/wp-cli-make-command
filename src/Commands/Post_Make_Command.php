<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliMake\DTOs\PostCreateResultDto;
use Vigihdev\WpCliMake\DTOs\PostUpdateResultDto;
use Vigihdev\WpCliMake\Factory\PostFactory;
use WP_CLI;
use WP_CLI_Command;

final class Post_Make_Command extends WP_CLI_Command
{
    /**
     * Buat post baru secara sederhana
     *
     * ## OPTIONS
     * 
     * <title>
     * : Judul post
     * 
     * [--content=<text>]
     * : Isi/konten post
     * 
     * [--type=<type>]
     * : Tipe post (post/page)
     *   ---
     *   default: post
     *   options:
     *     - post
     *     - page
     * 
     * [--status=<status>]
     * : Status post
     *   ---
     *   default: draft
     *   options:
     *     - draft
     *     - publish
     *     - pending
     * 
     * [--author=<id>]
     * : ID author
     *   ---
     *   default: 1
     * 
     * ## EXAMPLES
     * 
     *     wp make:post "Judul Post Pertama"
     *     wp make:post "Halaman About" --type=page --status=publish
     *     wp make:post "Post dengan content" --content="Isi dari post"
     *
     * @param array $args
     * @param array $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        $title = $args[0] ?? '';

        if (empty($title)) {
            WP_CLI::error('Post title is required');
        }

        $result = PostFactory::create($title, $assoc_args);

        $this->handleResult($result, 'created');
    }

    /**
     * Update existing post
     *
     * @subcommand update
     */
    public function updatePost(array $args, array $assoc_args): void
    {
        $post_id = (int) ($args[0] ?? 0);

        if ($post_id <= 0) {
            WP_CLI::error('Valid post ID is required');
        }

        $result = PostFactory::update($post_id, $assoc_args);

        $this->handleResult($result, 'updated');
    }

    /**
     * Handle operation result
     */
    private function handleResult($result, string $operation): void
    {
        if ($result instanceof PostCreateResultDto) {
            if ($result->isDuplicate()) {
                WP_CLI::warning('⚠️  Duplicate post found');
                WP_CLI::line('Use --unique_title flag to create with unique title');
                return;
            }

            if ($result->isCreated()) {
                WP_CLI::success("✅ Post {$operation} successfully!");
                $this->displayPostInfo($result->getPost());
                return;
            }
        }

        if ($result instanceof PostUpdateResultDto) {
            if ($result->isUpdated()) {
                WP_CLI::success("✅ Post {$operation} successfully!");
                $this->displayPostInfo($result->getPost());
                return;
            }
        }

        WP_CLI::error("Failed to {$operation} post: " . ($result->getError() ?? 'Unknown error'));
    }

    /**
     * Display post information
     */
    private function displayPostInfo(?\WP_Post $post): void
    {
        if (!$post) {
            return;
        }

        $status_labels = [
            'publish' => '📗 Published',
            'draft'   => '📝 Draft',
            'pending' => '⏳ Pending',
            'future'  => '⏰ Scheduled',
            'private' => '🔒 Private'
        ];

        $status = $status_labels[$post->post_status] ?? $post->post_status;

        WP_CLI::line("\n📋 Post Details:");
        WP_CLI::line("  ID: {$post->ID}");
        WP_CLI::line("  Title: {$post->post_title}");
        WP_CLI::line("  Type: {$post->post_type}");
        WP_CLI::line("  Status: {$status}");
        WP_CLI::line("  Author: " . get_the_author_meta('display_name', $post->post_author));
        WP_CLI::line("  Date: " . get_the_date('Y-m-d H:i', $post));
        WP_CLI::line("  URL: " . get_permalink($post->ID));
        WP_CLI::line("  Admin: " . admin_url("post.php?post={$post->ID}&action=edit"));
    }

    /**
     * Check if post exists
     *
     * @subcommand exists
     */
    public function exists(array $args): void
    {
        $post_id = (int) ($args[0] ?? 0);

        if ($post_id <= 0) {
            WP_CLI::error('Valid post ID is required');
        }

        $post = PostFactory::find($post_id);

        if ($post) {
            WP_CLI::success("✅ Post #{$post_id} exists");
            $this->displayPostInfo($post);
        } else {
            WP_CLI::warning("⚠️  Post #{$post_id} not found");
        }
    }
}
