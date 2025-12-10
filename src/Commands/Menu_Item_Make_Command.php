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
        WP_CLI::success(
            sprintf('Execute Command from class %s', self::class)
        );
    }
}
