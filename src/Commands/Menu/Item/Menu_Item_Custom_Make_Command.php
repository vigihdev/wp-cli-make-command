<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu\Item;


final class Menu_Item_Custom_Make_Command extends Base_Menu_Item_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-custom');
    }

    /**
     * Membuat item menu custom WordPress
     *
     * ## OPTIONS
     * 
     * <menu>
     * : The name, slug, or term ID for the menu.
     * required: true
     * 
     * <title>
     * : Title for the link.
     * required: true
     * 
     * <link>
     * : Target URL for the link.
     * required: true
     * 
     * [--description=<description>]
     * : Set a custom description for the menu item.
     * 
     * [--attr-title=<attr-title>]
     * : Set a custom title attribute for the menu item.
     * 
     * [--target=<target>]
     * : Set a custom link target for the menu item.
     * 
     * [--classes=<classes>]
     * : Set a custom link classes for the menu item.
     * 
     * [--position=<position>]
     * : Specify the position of this menu item.
     * 
     * [--parent-id=<parent-id>]
     * : Make this menu item a child of another menu item.
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     # Membuat item menu custom dari argumen
     *     wp make:menu-item-custom primary Example https://example.com
     * 
     *     # Membuat item menu custom dari argumen dengan dry run
     *     wp make:menu-item-custom primary Example https://example.com --dry-run
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
