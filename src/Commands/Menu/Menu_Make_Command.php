<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu;

use WP_CLI;
use WP_CLI\Utils;

final class Menu_Make_Command extends Base_Menu_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:menu');
    }

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
     * @param array $args
     * @param array $assoc_args 
     * @return void
     *
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        parent::__invoke($args, $assoc_args);
        $this->menu = $args[0];
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        try {
            if ($dryRun) {
                $this->dryRun();
                return;
            }
            $this->process();
        } catch (\Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }

    private function dryRun() {}

    private function process() {}
}
