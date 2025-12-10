<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliModels\UI\CliStyle;
use WP_CLI;

final class Menu_Item_Import_Make_Command extends Base_Import_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-import');
    }

    /**
     * Membuat item menu WordPress dari file JSON atau CSV
     *
     * ## OPTIONS
     *
     * <file>
     * : Path ke file JSON atau CSV yang berisi konfigurasi item menu
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     # Membuat item menu dari file JSON
     *     wp make:menu-item-import menu-items.json
     * 
     *     wp make:menu-item-import menu-items.json --dry-run
     *
     *     # Membuat item menu dari file CSV
     *     wp make:menu-item-import menu-items.csv
     *     wp make:menu-item-import menu-items.csv --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args Argumen
     * @param array $assoc_args Argumen asosiatif
     * @return void
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
