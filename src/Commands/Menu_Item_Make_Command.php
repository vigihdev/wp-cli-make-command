<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use WP_CLI;
use WP_CLI_Command;

final class Menu_Item_Make_Command extends WP_CLI_Command
{
    /**
     * Add an item to a navigation menu
     *
     * ## OPTIONS
     *
     * <menu-id>
     * : The ID or slug of the menu to add item to
     *
     * <title>
     * : The title of the menu item
     *
     * <url>
     * : The URL for the menu item
     *
     * [--parent=<parent-id>]
     * : ID of the parent menu item for creating submenus
     *
     * [--classes=<classes>]
     * : CSS classes for the menu item (comma-separated)
     *
     * [--target=<target>]
     * : Link target attribute
     * ---
     * default: _self
     * options:
     *   - _self
     *   - _blank
     * ---
     *
     * [--attr-title=<attr-title>]
     * : Title attribute for the link
     *
     * ## EXAMPLES
     *
     *     # Add a home link
     *     $ wp make:menu-item 15 "Home" "/"
     *
     *     # Add external link with target blank
     *     $ wp make:menu-item primary "Google" "https://google.com" --target=_blank
     *
     *     # Add submenu item
     *     $ wp make:menu-item main-menu "Sub Page" "/subpage" --parent=20
     *
     * @param array $args Positional arguments: [menu_id, title, url]
     * @param array $assoc_args Associative arguments for menu item options
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        // Default success message
        if (empty($args)) {
            WP_CLI::success(
                sprintf('Execute Command from class %s', self::class)
            );
            WP_CLI::log('Usage: wp make:menu-item <menu-id> <title> <url>');
            return;
        }

        // Contoh implementasi membuat menu item
        if (count($args) < 3) {
            WP_CLI::error('Please provide: menu-id, title, and url');
            return;
        }

        list($menu_id, $title, $url) = $args;

        $item_data = [
            'menu-item-title' => $title,
            'menu-item-url' => $url,
            'menu-item-status' => 'publish'
        ];

        $item_id = wp_update_nav_menu_item($menu_id, 0, $item_data);

        if (is_wp_error($item_id)) {
            WP_CLI::error('Failed to create menu item: ' . $item_id->get_error_message());
            return;
        }

        WP_CLI::success("Menu item '{$title}' created successfully (ID: {$item_id})");
    }

    /**
     * Help documentation
     */
    public static function help()
    {
        WP_CLI::log('Usage: wp make:menu-item <menu-id> <title> <url> [options]');
        WP_CLI::log('');
        WP_CLI::log('Options:');
        WP_CLI::log('  --parent=<parent-id>   Parent menu item ID');
        WP_CLI::log('  --classes=<classes>    CSS classes (comma separated)');
        WP_CLI::log('  --target=<target>      Link target (_blank, _self)');
        WP_CLI::log('');
        WP_CLI::log('Examples:');
        WP_CLI::log('  wp make:menu-item 15 "Home" "/"');
        WP_CLI::log('  wp make:menu-item 15 "About" "/about" --parent=20');
    }
}
