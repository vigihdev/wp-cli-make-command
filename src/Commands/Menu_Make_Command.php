<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Throwable;
use WP_CLI\Utils;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\Validators\MenuValidator;
use WP_CLI;
use WP_CLI_Command;

final class Menu_Make_Command extends Base_Command
{
    private ?string $menuName = null;

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
     * required: true
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
        $this->menuName = $args[0] ?? null;
        $io = new CliStyle();
        try {
            MenuValidator::validate($this->menuName)
                ->mustExist();
        } catch (Throwable $e) {
            $this->exceptionHandler->handle($io, $e);
        }
    }

    private function processDryRun(CliStyle $io) {}

    private function process(CliStyle $io) {}
}
