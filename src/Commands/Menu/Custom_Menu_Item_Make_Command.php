<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu;

use Vigihdev\WpCliMake\Validators\MenuItemCustomValidator;
use Vigihdev\WpCliModels\DTOs\Args\Menu\CustomItemMenuArgsDto;
use Vigihdev\WpCliModels\Entities\{MenuEntity, MenuItemEntity, MenuItemCustomEntity};
use WP_CLI\Utils;

final class Custom_Menu_Item_Make_Command extends Base_Menu_Item_Command
{
    private CustomItemMenuArgsDto $itemMenu;

    private array $menuItemData = [];

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
     *
     * <title>
     * : Title for the link.
     *
     * <link>
     * : Target URL for the link.
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
    public function __invoke(array $args, array $assoc_args): void
    {

        parent::__invoke($args, $assoc_args);
        $this->menu  = $args[0] ?? '';
        $this->title = $args[1] ?? '';
        $this->link  = $args[2] ?? '';
        $dryRun      = Utils\get_flag_value($assoc_args, 'dry-run', false);

        $this->itemMenu     = $this->instanceCustomMenuItem($assoc_args);
        $this->menuItemData = $this->transformMenuItemData($assoc_args);

        try {

            MenuItemCustomValidator::validate($this->itemMenu)->validateCreate();

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
        $io->title(sprintf('🔍 DRY RUN - Preview Menu Item'));
        $io->note('Tidak ada perubahan ke database');

        $io->newLine();
        $io->definitionList("Detail Menu Item", [
            'Menu'  => $this->menu,
            'Title' => $this->title,
            'Link'  => $this->link,
        ]);

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }

    private function process()
    {

        $io = $this->io;

        $insert = MenuItemEntity::create($this->menu, $this->menuItemData);
        if (is_wp_error($insert)) {
            $io->errorBlock(
                sprintf("Failed created menu item custom with name: %s : %s", $this->menu, $insert->get_error_message())
            );
            $io->newLine();
            return;
        }
        $io->successBlock(sprintf("Menu Item Custom created successfully with ID: %d menu: %s title: %s", $insert, $this->menu, $this->title));
        $io->newLine();
    }
}
