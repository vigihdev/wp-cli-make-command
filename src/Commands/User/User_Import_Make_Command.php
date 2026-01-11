<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\User;

use Vigihdev\Support\Collection;
use WP_CLI\Utils;

final class User_Import_Make_Command extends Base_User_Command
{
    /**
     * @var Collection<PostDto> $collection
     */
    private ?Collection $collection = null;

    public function __construct()
    {
        parent::__construct(name: 'make:user-import');
    }

    /**
     * Import User from CSV or JSON file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to CSV or JSON file.
     *
     * [--dry-run]
     * : Process import without creating any user.
     *
     * ## EXAMPLES
     *
     *     wp make:user-import kota.csv --dry-run
     *     wp make:user-import kota.json --dry-run
     * @when after_wp_load
     *
     * @param array $args
     * @param array $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        parent::__invoke($args, $assoc_args);
        $this->filepath = $args[0];
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        try {
            $this->normalizeFilePath();

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
        $io->title("🔍 DRY RUN - Preview Data Insert User");
        $io->note('Tidak ada perubahan ke database');

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }
    private function process()
    {
        $io = $this->io;
        $collection = $this->collection;
        $importIo = $this->importIo;

        // Task
        $io->newLine();
        $io->section("Start Insert Post Page: {$collection->count()} items");

        foreach ($collection->getIterator() as $index => $post) {
            // $postData = $this->mapPostData($post);

            // $importIo->start($post->getTitle());
            // usleep(2000000);
            // try {

            //     PostTypeValidator::validate($post)->validateCreate();
            //     PostFactoryValidator::validate($postData)->validateCreate();

            //     $insert = PostEntity::create($postData);
            //     if (is_wp_error($insert)) {
            //         $importIo->failed(sprintf("%s : %s", $post->getTitle(), $insert->get_error_message()));
            //         continue;
            //     }

            //     $importIo->success(sprintf("%s : ID %d", $post->getTitle(), $insert));
            // } catch (\Throwable $e) {
            //     $importIo->failed(sprintf("%s : %s", $post->getTitle(), $e->getMessage()));
            // }
        }
    }
}
