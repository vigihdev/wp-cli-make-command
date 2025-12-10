<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\DTOs\Fields\MenuItemChildrenFieldDto;
use Vigihdev\WpCliModels\Entities\MenuEntity;
use Vigihdev\WpCliModels\Entities\MenuItemEntity;
use Vigihdev\WpCliModels\UI\Components\{DryRunPresetImport, ProcessImportPreset};
use WP_CLI\Utils;
use WP_CLI;
use WP_Term;

final class Menu_Item_Children_Import_Make_Command extends Base_Import_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-children-import');
    }

    /**
     * Import Menu Item Children from JSON file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to JSON file.
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     $ wp make:menu-item-children-import menu-children.json
     *
     * @param array $args
     * @param array $assoc_args Associative arguments
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        $filepath = isset($args[0]) ? $args[0] : null;
        $io = new CliStyle();

        $this->validateFilePath($filepath, $io);
        $filepath = $this->normalizeFilePath($filepath);
        $this->validateFileJson($filepath, $io);

        $this->executeCommand($filepath, $assoc_args, $io);
    }

    protected function executeCommand(string $filepath, array $assoc_args, CliStyle $io): void
    {
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run');
        try {
            $collection = $this->loadDataDto($filepath, $io, MenuItemChildrenFieldDto::class);
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
        $dryRun = new DryRunPresetImport($io, $filepath, 'Term', $collection->count());
        $rows = [];
        foreach ($collection->getIterator() as $index => $menu) {
            /** @var MenuItemChildrenFieldDto $menu */

            $rows[] = [
                $index + 1,
            ];
        }
        $dryRun->renderCompact($rows, ['No', 'name', 'taxonomy', 'slug']);
    }

    private function processImport(string $filepath, Collection $collection, CliStyle $io)
    {
        $preset = new ProcessImportPreset(
            io: $io,
            filepath: $filepath,
            startTime: microtime(true),
            name: 'Term',
            total: $collection->count()
        );

        $preset->startRender();

        foreach ($collection->getIterator() as $index => $menu) {
            /** @var MenuItemChildrenFieldDto $menu */
            $preset->getProgressLog()->processing($menu->getLabel());

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
