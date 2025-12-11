<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\DTOs\Fields\MenuItemFieldDto;
use Vigihdev\WpCliModels\Entities\MenuEntity;
use Vigihdev\WpCliModels\Entities\MenuItemEntity;
use Vigihdev\WpCliModels\Enums\MenuItemType;
use Vigihdev\WpCliModels\Exceptions\MenuException;
use Vigihdev\WpCliModels\Exceptions\MenuItemException;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\UI\Components\{DryRunPresetImport, ProcessImportPreset};
use Vigihdev\WpCliModels\Validators\Entity\MenuItemValidator;
use Vigihdev\WpCliModels\Validators\Entity\MenuValidator;
use WP_CLI\Utils;
use WP_CLI;

final class Menu_Item_Children_Import_Make_Command extends Base_Import_Command
{
    private ?string $menuName = null;
    private int $parentId = 0;

    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-children-import');
    }

    /**
     * Membuat item menu WordPress dari file JSON
     *
     * ## OPTIONS
     * 
     * <menu-name>
     * : Name menu yang menjadi rujukan item menu
     * 
     * <parent-id>
     * : Parent name item menu yang menjadi rujukan item children menu
     * 
     * <file>
     * : Path ke file JSON yang berisi konfigurasi item menu
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     # Membuat item menu dari file JSON
     *     wp make:menu-item-children-import primary blog menu-items.json
     * 
     *     wp make:menu-item-children-import primary blog menu-items.json --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args Argumen
     * @param array $assoc_args Argumen asosiatif
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        $this->menuName = $args[0] ?? null;
        $this->parentId = isset($args[1]) ? (int) $args[1] : null;
        $filepath = $args[2] ?? null;
        $io = new CliStyle();

        try {

            $menu = MenuValidator::validate($this->menuName)
                ->mustExist();

            $parentItem = MenuItemValidator::validate($this->parentId)
                ->mustExist()
                ->mustBeMenuItem()
                ->mustBelongToMenu(MenuEntity::get($this->menuName)->getTermId())
                ->mustBeParentItem()
                ->mustBePublished()
                ->getMenuItem();

            $this->validateFilePath($filepath, $io);
            $filepath = $this->normalizeFilePath($filepath);
            $this->validateFileJson($filepath, $io);

            $this->executeCommand($filepath, $assoc_args, $io);
        } catch (MenuException $e) {
            $io->errorWithIcon($e->getMessage());
        } catch (MenuItemException $e) {
            $io->errorWithIcon($e->getMessage());
        }
    }

    protected function executeCommand(string $filepath, array $assoc_args, CliStyle $io): void
    {
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run');
        try {
            $collection = $this->loadDataDto($filepath, $io, MenuItemFieldDto::class);
            if ($dryRun) {
                $this->processDryRun($filepath, $collection, $io);
                return;
            }
            $this->processImport($filepath, $collection, $io);
        } catch (\Exception $e) {
            $io->errorWithIcon('❌ Error load data import: ' . $e->getMessage());
        }
    }

    private function processDryRun(string $filepath, Collection $collection, CliStyle $io)
    {
        $dryRun = new DryRunPresetImport($io, $filepath, 'Menu Item Children', $collection->count());
        $post = get_post($this->parentId);
        $rows = [];
        foreach ($collection->getIterator() as $index => $menu) {
            /** @var MenuItemFieldDto $menu */

            $rows[] = [
                $index + 1,
                $this->menuName,
                $post->post_title,
                $menu->getType(),
                $menu->getTitle(),
                $menu->getUrl()
            ];
        }
        $dryRun->renderCompact($rows, ['No', 'menu', 'parent', 'type', 'title', 'url']);
    }

    private function processImport(string $filepath, Collection $collection, CliStyle $io)
    {

        $menuName = $this->menuName;
        $preset = new ProcessImportPreset(
            io: $io,
            filepath: $filepath,
            startTime: microtime(true),
            name: 'Menu Item Children',
            total: $collection->count()
        );

        $preset->startRender();

        foreach ($collection->getIterator() as $index => $item) {
            /** @var MenuItemFieldDto $item */
            $preset->getProgressLog()->processing($item->getTitle());

            if (!MenuEntity::exists($menuName)) {
                $preset->getProgressLog()->warning("Menu {$menuName} tidak tersedia");
                continue;
            }

            $suportTypes = array_column(MenuItemType::cases(), 'value');
            if (! in_array($item->getType(), $suportTypes)) {
                $preset->getProgressLog()->warning("Menu type {$item->getType()} tidak support");
                $preset->getSummary()->addSkipped();
                continue;
            }

            $exist = MenuItemEntity::existByTitle($menuName, $item->getType(), $item->getTitle());
            if ($exist) {
                $preset->getProgressLog()->warning("Menu title : {$item->getTitle()} type : {$item->getType()} sudah di gunakan");
                $preset->getSummary()->addSkipped();
                continue;
            }

            $itemChildren = array_merge(['menu-item-parent-id' => $this->parentId], $item->toWpFormat());
            $create = MenuItemEntity::create($menuName, $itemChildren);
            if (is_wp_error($create)) {
                $io->errorWithIcon("Gagal create menu title {$item->getTitle()} {$create->get_error_message()}");
                $preset->getSummary()->addFailed();
            } else {
                $io->successWithIcon("Berhasil create menu item post ID {$create}");
                $preset->getSummary()->addSuccess();
            }
        }

        $preset->getProgressLog()->end();
        $preset->getSummary()->renderCompact($io, $filepath, (microtime(true) -  $preset->getStartTime()));
    }
}
