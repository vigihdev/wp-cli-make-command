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
        if (count($args) < 3) {
            WP_CLI::error('Usage: wp make:menu-item <menu-id> <title> <url>');
            WP_CLI::log('Example: wp make:menu-item primary "Google" "https://google.com"');
            return;
        }

        list($menu_identifier, $title, $url) = $args;

        // Get menu object (by ID or slug)
        $menu = null;
        if (is_numeric($menu_identifier)) {
            $menu = wp_get_nav_menu_object((int) $menu_identifier);
        } else {
            $menu = wp_get_nav_menu_object($menu_identifier);
        }

        if (!$menu) {
            WP_CLI::error("Menu not found: '{$menu_identifier}'");
            WP_CLI::log("Available menus:");
            WP_CLI::run_command(['menu', 'list']);
            return;
        }

        WP_CLI::log("Adding item to menu: '{$menu->name}' (ID: {$menu->term_id})");

        // Create menu item
        $item_id = wp_update_nav_menu_item($menu->term_id, 0, [
            'menu-item-title' => sanitize_text_field($title),
            'menu-item-url' => esc_url_raw($url),
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom',
        ]);

        if (is_wp_error($item_id)) {
            WP_CLI::error('Failed to create menu item: ' . $item_id->get_error_message());
            return;
        }

        // Verify
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        $new_item = get_post($item_id);

        WP_CLI::success("✅ Menu item created successfully!");
        WP_CLI::log("  Item ID: {$item_id}");
        WP_CLI::log("  Menu ID: {$menu->term_id}");
        WP_CLI::log("  Total items in menu: " . count($menu_items));

        // Show the new item
        WP_CLI::run_command(['menu', 'item', 'list', $menu->term_id]);
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
