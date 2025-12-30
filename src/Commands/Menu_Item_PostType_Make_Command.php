<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Throwable;
use Vigihdev\WpCliModels\Entities\MenuItemEntity;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\Validators\MenuItemValidator;
use WP_CLI\Utils;


final class Menu_Item_PostType_Make_Command extends Base_Command
{

    private string $menuName = '';

    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-post-type');
    }

    /**
     * Membuat item menu post type WordPress
     * 
     * ## OPTIONS
     * 
     * <menu>
     * : The name, slug, or term ID for the menu.
     * 
     * <post-id>
     * : Post ID to add to the menu.
     * 
     * [--title=<title>]
     * : Set a custom title for the menu item.
     * 
     * [--link=<link>]
     * : Set a custom url for the menu item.
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
     * [--porcelain]
     * : Output just the new menu item id.
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     * 
     *     # Membuat item menu post type
     *     wp make:menu-item-post-type primary 22222
     * 
     *     # Membuat item menu post type dengan dry run
     *     wp make:menu-item-post-type primary 22222 --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args Argumen
     * @param array $assoc_args Argumen asosiatif
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        $io = new CliStyle();
        $this->menuName = $args[0] ?? null;

        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        try {
            MenuItemValidator::validate(22222)
                ->mustExist();
            if ($dryRun) {
                $this->processDryRun($io, $assoc_args);
                return;
            }
            // $this->process($io, $assoc_args);
        } catch (Throwable $e) {
            $this->exceptionHandler->handle($io, $e);
        }
    }


    private function processDryRun(CliStyle $io, array $assoc_args)
    {
        $assoc_args = array_filter($assoc_args, function ($key) {
            return !in_array($key, ['dry-run'], true);
        }, ARRAY_FILTER_USE_KEY);

        $menuItemData = array_merge([], $assoc_args);

        $dryRun = $io->renderDryRunPreset("New Menu Item Children");
        $dryRun
            ->addDefinition($menuItemData)
            ->addInfo("1 Menu Item Children akan dibuat")
            ->render();
    }

    private function process(CliStyle $io, array $assoc_args)
    {

        $menuItemData = array_merge([], $assoc_args);

        $insert = MenuItemEntity::create('', $menuItemData);

        if (is_wp_error($insert)) {
            $io->renderBlock("Error insert menu item: " . $insert->get_error_message())->error();
            return;
        }

        $io->renderBlock(
            sprintf("Menu Item created successfully with ID : %d", $insert)
        )->success();
    }
}
