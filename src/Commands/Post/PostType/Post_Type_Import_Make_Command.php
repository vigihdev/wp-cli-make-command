<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\PostType;

use Vigihdev\Support\Collection;
use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;
use Vigihdev\WpCliMake\DTOs\PostDto;
use Vigihdev\WpCliMake\Support\DtoJsonTransformer;
use Vigihdev\WpCliMake\Validators\PostFactoryValidator;
use Vigihdev\WpCliMake\Validators\PostTypeValidator;
use Vigihdev\WpCliModels\Entities\PostEntity;
use Vigihdev\WpCliModels\Enums\PostType;
use WP_CLI\Utils;

final class Post_Type_Import_Make_Command extends Base_Post_Command
{
    /**
     * @var Collection<PostDto> $collection
     */
    private ?Collection $collection = null;

    public function __construct()
    {
        parent::__construct(name: 'make:post-type-import');
    }

    /**
     * Buat tipe post baru secara sederhana
     *
     * ## OPTIONS
     *
     * <file>
     * : File post type import
     *
     * [--dry-run]
     * : Run the command in dry-run mode
     *
     * ## EXAMPLES
     *
     *     # Import a new post type
     *     wp make:post-type-import event.json --dry-run
     *
     * @param array $args array index
     * @param array $assoc_args array of associative arguments
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        parent::__invoke($args, $assoc_args);
        $this->filepath = $args[0];
        $dryRun         = Utils\get_flag_value($assoc_args, 'dry-run', false);

        $io = $this->io;
        try {

            $this->normalizeFilePath();
            $this->validateFilepathJson();
            $this->collection = DtoJsonTransformer::fromFile($this->filepath, PostDto::class);
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

        $io         = $this->io;
        $collection = $this->collection;

        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Post Type");
        $io->note('Tidak ada perubahan ke database');

        $io->write(['<fg=cyan>Source File:</>', "<fg=yellow>{$this->filepath}</>"]);
        $io->write(['<fg=cyan>Total Items Post:</>', "<fg=yellow>{$collection->count()}</>"]);

        $io->newLine();
        $io->table(
            headers: ['Title', 'Type', 'Meta Input'],
            rows: $collection->map(function (PostDto $post) {
                return [
                    $post->getTitle(),
                    PostType::PAGE->value,
                    json_encode($post->getMetaInput(), JSON_UNESCAPED_SLASHES),
                ];
            })->toArray(),
        );

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
    }

    private function process(): void
    {

        $io         = $this->io;
        $collection = $this->collection;
        $importIo   = $this->importIo;

        // Task
        $io->newLine();
        $io->section("Start Insert Post Page: {$collection->count()} items");

        foreach ($collection->getIterator() as $index => $post) {
            $postData = $this->mapPostData($post);

            $importIo->start($post->getTitle());
            usleep(2000000);
            try {

                PostTypeValidator::validate($post)->validateCreate();
                PostFactoryValidator::validate($postData)->validateCreate();

                $insert = PostEntity::create($postData);
                if (is_wp_error($insert)) {
                    $importIo->failed(sprintf("%s : %s", $post->getTitle(), $insert->get_error_message()));
                    continue;
                }

                $importIo->success(sprintf("%s : ID %d", $post->getTitle(), $insert));
            } catch (\Throwable $e) {
                $importIo->failed(sprintf("%s : %s", $post->getTitle(), $e->getMessage()));
            }
        }
    }


    private function mapPostData(PostDto $post): array
    {
        $postDefault = $this->loadDefaultPost($post->getTitle());
        $postData    = array_merge(
            $postDefault->toArray(),
            $post->toArray(),
            $this->loadAuthorStatus()
        );
        return $postData;
    }
}
