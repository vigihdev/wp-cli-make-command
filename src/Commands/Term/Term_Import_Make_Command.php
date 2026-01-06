<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Term;

use WP_CLI\Utils;

final class Term_Import_Make_Command extends Base_Term_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:term-import');
    }


    /**
     * Import terms from CSV or JSON file.
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
     *     wp make:term-import kota.csv
     *     wp make:term-import kota.json
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args)
    {
        parent::__invoke($args, $assoc_args);
    }

    private function dryRun() {}

    private function process() {}
}
