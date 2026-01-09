<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu;

use Vigihdev\WpCliMake\Validators\MenuValidator;
use Vigihdev\WpCliModels\Entities\MenuEntity;
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
        $this->location = Utils\get_flag_value($assoc_args, 'location');
        $this->description = Utils\get_flag_value($assoc_args, 'description');
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        try {

            MenuValidator::validate($this->menu)
                ->validateCreate();

            if ($dryRun) {
                $this->dryRun();
                return;
            }
            $this->process();
        } catch (\Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }

    private function dryRun()
    {
        $io = $this->io;

        $io->newLine();
        $io->title(sprintf('🔍 DRY RUN - Preview Menu "%s"', $this->menu));
        $io->note('Tidak ada perubahan ke database');

        $io->newLine();
        $io->definitionList("Detail Menu:", [
            'Menu Name' => $this->menu,
            'Slug' => sanitize_title($this->menu),
            'Location' => $this->location ?? 'N/A',
            'Description' => $this->description ?? 'N/A',
        ]);

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }

    private function process()
    {
        $io = $this->io;

        $insert = MenuEntity::create($this->menu, $this->mapMenuData());
        if (is_wp_error($insert)) {
            $io->errorBlock(sprintf("Failed created menu with name: %s : %s", $this->menu, $insert->get_error_message()));
        } else {
            $io->successBlock(sprintf("Success created menu with ID: %d and name: %s", $insert, $this->menu));
        }
    }

    private function mapMenuData(): array
    {
        $menuData = [];

        if ($this->location) {
            $menuData['location'] = $this->location;
        }

        if ($this->description) {
            $menuData['description'] = $this->description;
        }

        if (!empty($menuData)) {
            $menuData['menu-name'] = $this->menu;
        }

        return $menuData;
    }
}
