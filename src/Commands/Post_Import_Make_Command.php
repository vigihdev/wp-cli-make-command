<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliModels\UI\CliStyle;

final class Post_Import_Make_Command extends Base_Import_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:post-import');
    }

    /**
     * ## OPTIONS
     *
     * <file>
     * : Path ke file JSON
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
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

        $filepath = isset($args[0]) ? $args[0] : null;
        $io = new CliStyle();

        $this->validateFilePath($filepath, $io);
        $filepath = $this->normalizeFilePath($filepath);
        $this->validateFileJson($filepath, $io);
    }
}
