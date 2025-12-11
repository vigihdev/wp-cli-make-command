<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\DTOs\Fields\MenuItemFieldDto;
use Vigihdev\WpCliModels\Entities\MenuEntity;
use Vigihdev\WpCliModels\Entities\MenuItemEntity;
use Vigihdev\WpCliModels\Enums\MenuItemType;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\UI\Components\{DryRunPresetImport, ProcessImportPreset};
use WP_CLI\Utils;
use WP_CLI;

final class Menu_Item_Import_Make_Command extends Base_Import_Command
{

    private string $menuName;
    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-import');
    }

    /**
     * Membuat item menu WordPress dari file JSON atau CSV
     *
     * ## OPTIONS
     * 
     * <menu-name>
     * : Name menu
     * 
     * <file>
     * : Path ke file JSON atau CSV yang berisi konfigurasi item menu
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     # Membuat item menu dari file JSON
     *     wp make:menu-item-import menu-items.json
     * 
     *     wp make:menu-item-import menu-items.json --dry-run
     *
     *     # Membuat item menu dari file CSV
     *     wp make:menu-item-import menu-items.csv
     *     wp make:menu-item-import menu-items.csv --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args Argumen
     * @param array $assoc_args Argumen asosiatif
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        $filepath = isset($args[0]) ? $args[0] : null;
        // $this->menuName = Utils\get_flag_value($assoc_args, 'dry-run');
        $io = new CliStyle();

        $this->validateFilePath($filepath, $io);
        $filepath = $this->normalizeFilePath($filepath);
        $this->validateFileJson($filepath, $io);
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
        $dryRun = new DryRunPresetImport($io, $filepath, 'Menu Item', $collection->count());
        $rows = [];
        foreach ($collection->getIterator() as $index => $menu) {
            /** @var MenuItemFieldDto $menu */

            $rows[] = [
                $index + 1,
                $menu->getType(),
                $menu->getTitle(),
                $menu->getUrl()
            ];
        }
        $dryRun->renderCompact($rows, ['No', 'type', 'title', 'url']);
    }

    private function processImport(string $filepath, Collection $collection, CliStyle $io)
    {
        $preset = new ProcessImportPreset(
            io: $io,
            filepath: $filepath,
            startTime: microtime(true),
            name: 'Menu Item',
            total: $collection->count()
        );

        $preset->startRender();

        foreach ($collection->getIterator() as $index => $item) {
            /** @var MenuItemFieldDto $item */
            $preset->getProgressLog()->processing($item->getTitle());
            $menuName = $this->menuName ?? '';
            if (!MenuEntity::exists($menuName)) {
                echo "⚠️ Menu {$menuName} tidak tersedia <br>";
                continue;
            }

            $suportTypes = array_column(MenuItemType::cases(), 'value');
            if (! in_array($item->getType(), $suportTypes)) {
                echo "⚠️ Menu type {$item->getType()} tidak support <br>";
                continue;
            }

            $exist = MenuItemEntity::existByTitle($menuName, $item->getType(), $item->getTitle());
            if ($exist) {
                echo "⚠️ Menu title : {$item->getTitle()} type : {$item->getType()} sudah di gunakan <br>";
                continue;
            }

            $create = MenuItemEntity::create($menuName, $item->toWpFormat());
            if (is_wp_error($create)) {
                echo "❌ Gagal create {$create->get_error_message()} <br>";
            } else {
                echo "✅ Success create menu item post ID {$create} <br>";
            }

            // $name = $term->getName();
            // $taxonomy = $term->getTaxonomy();
            // $args = array_filter($term->toArray(), fn($k) => !in_array($k, ['name', 'taxonomy']), ARRAY_FILTER_USE_KEY);

            // $exist = TermEntity::findByName($name, $taxonomy);
            // if ($exist instanceof WP_Term) {
            //     $preset->getProgressLog()->warning("Term '{$name}' sudah ada, dilewati.");
            //     $preset->getSummary()->addSkipped();
            //     continue;
            // }

            // $create = MenuEntity::create($name, $taxonomy, $args);
            // if (is_wp_error($create)) {
            //     $io->errorWithIcon("Gagal create '{$name}': " . $create->get_error_message());
            //     $preset->getSummary()->addFailed();
            // } else {
            //     $io->successWithIcon("Berhasil create term_id {$create['term_id']}");
            //     $preset->getSummary()->addSuccess();
            // }
        }

        $preset->getProgressLog()->end();
        $preset->getSummary()->renderCompact($io, $filepath, (microtime(true) -  $preset->getStartTime()));
    }
}
