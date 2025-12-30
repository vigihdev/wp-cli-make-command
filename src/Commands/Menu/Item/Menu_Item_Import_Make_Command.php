<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu\Item;


final class Menu_Item_Import_Make_Command extends Base_Menu_Item_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-import');
    }

    /**
     * Membuat item menu WordPress dari file JSON
     *
     * ## OPTIONS
     * 
     * <menu-name>
     * : Name menu yang menjadi rujukan item menu
     * 
     * <file>
     * : Path ke file JSON yang berisi konfigurasi item menu
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     # Membuat item menu dari file JSON
     *     wp make:menu-item-import primary menu-items.json
     * 
     *     wp make:menu-item-import primary menu-items.json --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args Argumen
     * @param array $assoc_args Argumen asosiatif
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void {}


    private function dryRun() {}

    private function process() {}
}
