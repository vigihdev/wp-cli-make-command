<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\Post;

use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;
use Vigihdev\Support\Collection;
use Vigihdev\WpCliMake\DTOs\PostDto;
use Vigihdev\WpCliMake\Support\DtoJsonTransformer;
use Vigihdev\WpCliModels\Entities\PostEntity;
use Vigihdev\WpCliModels\Enums\PostStatus;
use Vigihdev\WpCliModels\Enums\PostType;
use Vigihdev\WpCliModels\Validators\PostCreationValidator;
use WP_CLI\Utils;

final class Post_Import_Make_Command extends Base_Post_Command
{

    /**
     * @var Collection<PostDto> $collection
     */
    private ?Collection $collection = null;

    public function __construct()
    {
        parent::__construct(name: 'make:post-import');
    }

    /**
     * 
     * Create post from JSON file import.
     * 
     * ## OPTIONS
     *
     * <file>
     * : Path file JSON yang berisi data post.
     * 
     * [--dry-run]
     * : Jalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun.
     *  
     * ## EXAMPLES
     * 
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

        $io = $this->io;
        $collection = $this->collection;

        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Post");
        $io->note('Tidak ada perubahan ke database');

        $io->newLine();
        $io->table(
            headers: ['Title', 'Type', 'Taxonomy'],
            rows: $collection->map(fn(PostDto $post) => [
                $post->getTitle(),
                $post->getType(),
                implode(', ', $post->getTaxInput()),
            ])->toArray()
        );

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }

    private function process(): void
    {

        $io = $this->io;
        $importIo = $this->importIo;
        $collection = $this->collection;

        // Task
        $io->newLine();
        $io->section("Start Insert Post: {$collection->count()} items");
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
                'post_type'    => PostType::POST->value,
            ]
        );
        return $postData;
    }
}
