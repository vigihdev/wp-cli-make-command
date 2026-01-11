<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu;

use Vigihdev\Support\Collection;
use Vigihdev\WpCliMake\Support\{DtoJsonTransformer, ImportSummary};
use Vigihdev\WpCliMake\Validators\MenuItemCustomValidator;
use Vigihdev\WpCliModels\DTOs\Args\Menu\CustomItemMenuArgsDto;
use Vigihdev\WpCliModels\Entities\MenuItemEntity;
use Vigihdev\WpCliModels\Enums\PostStatus;
use WP_CLI\Utils;

final class Custom_Import_Menu_Item_Make_Command extends Base_Menu_Item_Command
{
    /**
     * @var Collection<CustomItemMenuArgsDto> $collection
     */
    private ?Collection $collection = null;

    public function __construct()
    {
        parent::__construct(name: 'make:menu-item-custom-import');
    }

    /**
     * Create Menu Item Custom Import from file JSON
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to the file containing menu item data.
     *
     * [--dry-run]
     * : Run the command in dry-run mode to preview changes without applying them.
     *
     * ## EXAMPLES
     *
     * # Import Menu Item Custom from JSON file
     * wp make:menu-item-custom-import ./data/json/menu/erorr-item-custom.json --dry-run
     *
     * # Import Menu Item Custom from JSON file without dry-run
     * wp make:menu-item-custom-import ./data/json/menu/erorr-item-custom.json
     *
     * @when after_wp_load
     *
     * @param array $args
     * @param array $assoc_args
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        parent::__invoke($args, $assoc_args);
        $this->filepath = $args[0];
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        try {
            $this->normalizeFilePath();
            $this->validateFilepathJson();
            $this->collection = DtoJsonTransformer::fromFile($this->filepath, CustomItemMenuArgsDto::class);
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
        $collection = $this->collection;

        $io->newLine();
        $io->title(sprintf('🔍 DRY RUN - Preview Menu Item'));
        $io->note('Tidak ada perubahan ke database');

        $io->newLine();
        $io->table(
            headers: ['Menu', 'Title', 'Link', 'Parent ID'],
            rows: $collection->map(function (CustomItemMenuArgsDto $item) {
                return [
                    $item->getMenu(),
                    $item->getTitle(),
                    $item->getLink(),
                    $item->getParentId(),
                ];
            })->toArray(),
        );

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }

    private function process()
    {
        $io = $this->io;
        $importIo = $this->importIo;
        $collection = $this->collection;
        $summary = new ImportSummary(total: $collection->count());

        // Task
        $io->newLine();
        $io->section("Start Insert Post: {$collection->count()} items");
        foreach ($collection->getIterator() as $index => $post) {
            $postData = $this->mapPostData($post);

            $importIo->start($post->getTitle());
            usleep(2000000);

            try {
                MenuItemCustomValidator::validate($post)->validateCreate();

                $insert = MenuItemEntity::create($post->getMenu(), $postData);
                if (is_wp_error($insert)) {
                    $summary->addFailed();
                    $importIo->failed(sprintf("%s : %s", $post->getTitle(), $insert->get_error_message()));
                    continue;
                }
                $summary->addSuccess();
                $importIo->success(sprintf("%s : ID %d", $post->getTitle(), $insert));
            } catch (\Throwable $e) {
                if ($e->getCode() === 409) {
                    $summary->addSkipped();
                    $importIo->skipped(sprintf('%s', $e->getMessage()));
                } else {
                    $summary->addFailed();
                    $importIo->failed(sprintf("%s", $e->getMessage()));
                }
            }
        }

        $io->newLine();
        $io->definitionList("📊 Summary", $summary->getResults());
    }

    private function mapPostData(CustomItemMenuArgsDto $post): array
    {
        $postData = array_merge($post->toArray(), [
            'status' => PostStatus::PUBLISH->value
        ]);
        return $postData;
    }
}
