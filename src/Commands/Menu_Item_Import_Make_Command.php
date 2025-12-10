<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use WP_CLI;
use WP_CLI_Command;

final class Menu_Item_Import_Make_Command extends Base_Import_Command
{


    /**
     * Membuat item menu WordPress dari file JSON atau CSV
     *
     * ## OPTIONS
     *
     * <file>
     * : Path ke file JSON atau CSV yang berisi konfigurasi item menu
     * 
     * [--dry-run]
     *  : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
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
        WP_CLI::success(
            sprintf('Execute Command from class %s', self::class)
        );
    }
}
