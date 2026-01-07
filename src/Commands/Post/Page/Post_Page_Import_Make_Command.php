<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\Page;

use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;
use Vigihdev\WpCliMake\DTOs\Posts\PostDto;
use Vigihdev\WpCliMake\Support\DtoJsonTransformer;
use Vigihdev\WpCliModels\Entities\PostEntity;
use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\Enums\PostStatus;
use Vigihdev\WpCliModels\Enums\PostType;
use Vigihdev\WpCliModels\Validators\PostCreationValidator;
use WP_CLI\Utils;

final class Post_Page_Import_Make_Command extends Base_Post_Command
{

    /**
     * @var Collection<PostDto> $collection
     */
    private ?Collection $collection = null;

    public function __construct()
    {
        parent::__construct(name: 'make:post-page-import');
    }

    /**
     * Membuat post halaman baru di WordPress dari file JSON.
     *
     * ## OPTIONS
     *
     * <file>
     * : File post yang akan diimpor dengan format .json.
     * 
     * [--dry-run]
     * : Melakukan simulasi tanpa benar-benar membuat post
     * 
     * 
     * @param array $args Argumen posisi
     * @param array $assoc_args Argumen opsional
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        parent::__invoke($args, $assoc_args);
        $this->filepath = $args[0];
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

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

    /**
     * Melakukan simulasi tanpa benar-benar membuat post.
     *
     * @return void
     */
    private function dryRun(): void
    {

        $io = $this->io;
        $collection = $this->collection;

        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Post Page");
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

    /**
     * Process the post creation. 
     *
     * @return void 
     */
    private function process(): void
    {

        $io = $this->io;
        $collection = $this->collection;
        $importIo = $this->importIo;

        // Task
        $io->newLine();
        $io->section("Start Insert Post Page: {$collection->count()} items");
        foreach ($collection->getIterator() as $index => $post) {
            $postData = $this->mapPostData($post);

            $importIo->start($post->getTitle());

            usleep(2000000);
            try {
                PostCreationValidator::validate($post->toArray())
                    ->mustHaveUniqueTitle($post->getTitle(), [PostType::NAV_MENU_ITEM->value]);

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
        $postData = array_merge(
            $postDefault->toArray(),
            $post->toArray(),
            [
                'post_author' => $this->author,
                'post_status'  => PostStatus::PUBLISH->value,
                'post_type'    => PostType::PAGE->value,
            ]
        );
        return $postData;
    }
}
