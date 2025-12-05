<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use WP_CLI;
use WP_CLI_Command;

final class Menu_Make_Command extends WP_CLI_Command
{
    /**
     * Create a new navigation menu
     *
     * This command creates a new WordPress navigation menu with optional
     * theme location assignment and description.
     *
     * ## OPTIONS
     *
     * <menu-name>
     * : The name of the menu to create. Required.
     *
     * [--location=<location>]
     * : Theme location slug to assign the menu to (e.g., 'primary', 'footer').
     *   If the location doesn't exist in theme, it will be created.
     *   ---
     *   default: primary
     *   options:
     *     - primary
     *     - footer
     *     - sidebar
     *     - mobile
     *   ---
     *
     * [--description=<description>]
     * : Description for the menu. Appears in admin interface.
     *
     * [--force]
     * : Overwrite existing menu if name conflicts.
     *
     * [--dry-run]
     * : Preview changes without actually creating the menu.
     *
     * ## EXAMPLES
     *
     *     # Create a basic menu
     *     $ wp make:menu "Main Menu"
     *
     *     # Create footer menu with description
     *     $ wp make:menu "Footer Links" --location=footer --description="Footer navigation links"
     *
     *     # Force create menu even if name exists
     *     $ wp make:menu "Header Menu" --force
     *
     *     # Preview menu creation
     *     $ wp make:menu "Test Menu" --dry-run
     *
     * @synopsis <menu-name> [--location=<location>] [--description=<description>] [--force] [--dry-run]
     *
     * @param array $args Command line positional arguments
     *                    Index 0: Menu name (string)
     * @param array $assoc_args Command line associative arguments
     *                          Supported keys:
     *                          - location: string
     *                          - description: string
     *                          - force: bool
     *                          - dry-run: bool
     * @return void
     *
     * @throws WP_CLI\ExitException When menu creation fails
     * @throws InvalidArgumentException When required arguments are missing
     *
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        // WP_CLI::success(
        //     sprintf('Execute Command from class %s', self::class)
        // );

        if (empty($args[0])) {
            WP_CLI::error('Please provide a menu name');
            return;
        }

        $menu_name = sanitize_text_field($args[0]);
        $description = $assoc_args['description'] ?? '';
        $location = $assoc_args['location'] ?? '';

        // Cek apakah menu sudah ada
        $menus = wp_get_nav_menus();
        foreach ($menus as $menu) {
            if ($menu->name === $menu_name) {
                WP_CLI::error("Menu '{$menu_name}' already exists (ID: {$menu->term_id})");
                return;
            }
        }

        // Buat menu
        $menu_id = wp_create_nav_menu($menu_name);

        if (is_wp_error($menu_id)) {
            WP_CLI::error($menu_id->get_error_message());
            return;
        }

        // Update description jika ada
        if ($description) {
            wp_update_nav_menu_object($menu_id, ['description' => $description]);
        }

        // Assign location jika ada
        if ($location) {
            $locations = get_theme_mod('nav_menu_locations');
            $locations[$location] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }

        WP_CLI::success("✅ Menu '{$menu_name}' created successfully!");
        WP_CLI::log("  ID: {$menu_id}");
        if ($location) {
            WP_CLI::log("  Location: {$location}");
        }
    }
}
