<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\Post;

use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;

final class Post_Import_Make_Command extends Base_Post_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:post-import');
    }

    /**
     * ## OPTIONS
     *
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     * 
     * @when after_wp_load
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void {}
}
