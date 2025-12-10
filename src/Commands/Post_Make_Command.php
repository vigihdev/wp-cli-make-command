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
        WP_CLI::success(
            sprintf('Execute Command from class %s', self::class)
        );
    }
}
