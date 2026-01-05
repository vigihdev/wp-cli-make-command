<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post;

use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\DTOs\Fields\{DefaultPostFieldDto, PostTypeFieldDto};
use WP_CLI\Utils;

final class Post_Type_Import_Make_Command extends Base_Post_Command
{

    /**
     * @var Collection<PostTypeFieldDto> $collection
     */
    private Collection $collection;

    public function __construct()
    {
        parent::__construct(name: 'make:post-type-import');
    }

    /**
     * Import Post Type dari file JSON.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path file JSON yang akan diimport. yang sudah terformat post type.
     * 
     * [--dry-run]
     * : Jalankan tanpa perubahan database.
     * 
     * ## EXAMPLES
     *
     *     wp make:post-type-import /path/to/posts.json
     *     wp make:post-type-import /path/to/posts.json --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        $this->filepath = $args[0];
        $dryRun = (bool)Utils\get_flag_value($assoc_args, 'dry-run', false);

        $io = $this->io;
        try {
            $this->normalizeFilePath();

            $this->validateFilepathJson(); // Memastikan file yang di import adalah file JSON
            $this->collection = $this->transformDto(PostTypeFieldDto::class);

            if ($dryRun) {
                $this->dryRun();
                return;
            }
            $this->process();
        } catch (\Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }

    private function dryRun(): void
    {
        $io = $this->io;
        $io->newLine();
        $io->title('🔍 DRY RUN - Preview Data Import');
        $io->note('Tidak ada perubahan ke database');
        $collection = $this->collection;

        $io->table(
            headers: ['Author', 'Type', 'Title', 'Status', 'Taxonomy'],
            rows: $collection->map(function (PostTypeFieldDto $post) {
                return [
                    $post->getAuthor(),
                    $post->getType(),
                    $post->getTitle(),
                    $post->getStatus(),
                    implode(', ', $post->getTaxInput()),
                    // $post->getContent(),
                ];
            })->toArray()
        );

        $io->newLine();
        $io->definitionList(
            '📊 Summary',
            [
                'Total Posts' => (string) $collection->count(),
                'Mode' => 'Dry Run',
                'File' => basename($this->filepath)
            ]
        );

        $io->success('Dry run selesai!');
        $io->note('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
    }

    private function process(): void
    {
        $io = $this->io;
        $collection = $this->collection;
        $io->title("Start Import Post Type");

        foreach ($collection->getIterator() as $index => $post) {
            /** @var PostTypeFieldDto $post */
            $author = $post->getAuthor();
            $type = $post->getType();
            $title = $post->getTitle();
            $status = $post->getStatus();
            $taxInput = $post->getTaxInput();
            $content = $post->getContent();


            $io->spinnerStart("<fg=yellow;options=bold>Process {$title}</>");
            if (in_array($type, ['post', 'page'])) {
                $io->spinnerStop("<fg=white;bg=red> FAILED </><fg=red> {$title} : not support {$type}</>");
                continue;
            }

            if (get_taxonomy($type)) {
                continue;
            }

            // validate author
            // validate title
            // validate post type
            // validate content
            // validate taxInput
            $io->spinnerStop("<fg=green;options=bold>✔ {$post->getTitle()} done</>");
        }
    }
}
