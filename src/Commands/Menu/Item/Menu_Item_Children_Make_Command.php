<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu\Item;


final class Menu_Item_Children_Make_Command extends Base_Menu_Item_Command
{


    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-children');
    }

    /**
     * Membuat item menu children WordPress
     * 
     * ## OPTIONS
     * 
     * <title>
     * : Title for the link children.
     * required: true
     * 
     * <link>
     * : Target URL for the link children.
     * required: true
     * 
     * --menu=<menu>
     * : Name menu yang menjadi rujukan item menu
     * required: true
     * ---
     * 
     * --parent-id=<parent-id>
     * : ID parent menu item dari Post ID yang akan menjadi parent
     * required: true
     * ---
     * 
     * [--description=<description>]
     * : Set a custom description for the menu item children.
     * 
     * [--attr-title=<attr-title>]
     * : Set a custom title attribute for the menu item children.
     * 
     * [--target=<target>]
     * : Set a custom link target for the menu item children.
     * 
     * [--classes=<classes>]
     * : Set a custom link classes for the menu item children.
     * 
     * [--position=<position>]
     * : Specify the position of this menu item children.
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     * 
     *     # Membuat item menu children custom dari argumen
     *     wp make:menu-item-children Example https://example.com --menu=primary --parent-id=123
     * 
     *     # Membuat item menu children custom dari argumen dengan dry run
     *     wp make:menu-item-children Example https://example.com --menu=primary --parent-id=123 --dry-run
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
