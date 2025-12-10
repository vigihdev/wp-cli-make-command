<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliModels\UI\CliStyle;

final class User_Import_Make_Command extends Base_Import_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:user-import');
    }

    /**
     * Import User from CSV or JSON file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to CSV or JSON file.
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     wp make:user-import kota.csv
     *     wp make:user-import kota.json
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
