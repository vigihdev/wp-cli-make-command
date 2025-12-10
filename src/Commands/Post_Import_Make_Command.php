<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use WP_CLI;
use WP_CLI_Command;

final class Post_Import_Make_Command extends WP_CLI_Command
{
    /**
     * ## OPTIONS
     *
     * <file>
     * : Path ke file JSON
     * 
     * [--dry-run]
     *  : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     wp make:post-import post.json
     * 
     *     wp make:post-import post-data.json --dry-run
     *
     * @when after_wp_load
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
