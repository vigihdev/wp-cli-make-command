<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post;


final class Post_Type_Make_Command extends Base_Post_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:post-type');
    }

    /**
     * Buat tipe post baru secara sederhana
     *
     * ## OPTIONS
     * 
     * <title>
     * : Judul post type
     * 
     * [--dry-run]
     * : Run the command in dry-run mode
     * ---
     * default: false
     * ---
     * 
     * ## EXAMPLES
     *
     *     # Create a new post type
     *     wp make:post-type event --dry-run
     * 
     * @param array $args array index
     * @param array $assoc_args array of associative arguments
     */
    public function __invoke(array $args, array $assoc_args): void {}
}
