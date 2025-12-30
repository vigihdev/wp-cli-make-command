<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Throwable;
use Vigihdev\WpCliModels\DTOs\Entities\Menu\MenuEntityDto;
use Vigihdev\WpCliModels\Entities\{MenuEntity, MenuItemEntity};
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\Validators\MenuItemValidator;
use Vigihdev\WpCliModels\Fields\MenuItemCustomField;
use WP_CLI\Utils;


final class Menu_Item_Custom_Make_Command extends Base_Command
{

    private ?MenuEntityDto $menuDto = null;

    private ?MenuItemCustomField $fieldItem = null;

    private string $menu = '';

    private string $title = '';

    private string $link = '';

    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-custom');
        $this->fieldItem = new MenuItemCustomField();
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
    public function __invoke(array $args, array $assoc_args): void
    {

        $io = new CliStyle();
        $this->menu = $args[0] ?? '';
        $this->title = $args[1] ?? '';
        $this->link = $args[2] ?? '';

        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        try {
            MenuItemValidator::validate(postId: null, menuId: $this->menu)
                ->mustMenuExist()
                ->mustBeUniqueCustomItem($this->title, $this->link);

            $this->menuDto = MenuEntity::get($this->menu);
            $assoc_args = array_merge($assoc_args, ['type' => 'custom', 'status' => 'publish']);

            if ($dryRun) {
                $this->processDryRun($io, $assoc_args);
                return;
            }

            $this->process($io, $assoc_args);
        } catch (Throwable $e) {
            $this->exceptionHandler->handle($io, $e);
        }
    }


    private function processDryRun(CliStyle $io, array $assoc_args)
    {
        $assoc_args = array_filter($assoc_args, function ($key) {
            return !in_array($key, ['dry-run'], true);
        }, ARRAY_FILTER_USE_KEY);

        $menuItemData = array_merge([
            'menu_name' => $this->menuDto->getSlug(),
            'title' => $this->title,
            'url' => $this->link,
        ], $assoc_args);

        $dryRun = $io->renderDryRunPreset("New Menu Item Custom");
        $dryRun
            ->addDefinition($menuItemData)
            ->addInfo("1 Menu Item Custom akan dibuat")
            ->render();
    }

    private function process(CliStyle $io, array $assoc_args)
    {

        $assoc_args = array_merge(['title' => $this->title, 'url' => $this->link], $assoc_args);

        $menuItemData = $this->fieldItem->transform($assoc_args);
        $insert = MenuItemEntity::create($this->menuDto->getName(), $menuItemData);

        if (is_wp_error($insert)) {
            $io->renderBlock("Error insert menu item custom: " . $insert->get_error_message())->error();
            return;
        }

        $io->renderBlock(
            sprintf("Menu Item Custom created successfully with ID : %d", $insert)
        )->success();
    }
}
