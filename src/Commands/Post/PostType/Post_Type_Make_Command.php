<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\PostType;

use Vigihdev\Support\Collection;
use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;

final class Post_Type_Make_Command extends Base_Post_Command
{

    /**
     * @var Collection<PostDto> $collection
     */
    private ?Collection $collection = null;

    public function __construct()
    {
        parent::__construct(name: 'make:post-type');
    }

    /**
     * Buat tipe post baru secara sederhana
     *
     * ## OPTIONS
     * 
     * <title>
     * : Judul post type
     * 
     * [--post_content=<post_content>]
     * : Isi konten post
     * required: true
     * 
     * [--dry-run]
     * : Run the command in dry-run mode
     * 
     * ## EXAMPLES
     *
     *     # Create a new post type
     *     wp make:post-type event --dry-run
     * 
     * @param array $args array index
     * @param array $assoc_args array of associative arguments
     */
    public function __invoke(array $args, array $assoc_args): void {}

    private function dryRun(): void
    {

        $io = $this->io;
        $collection = $this->collection;

        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Post Type");
        $io->note('Tidak ada perubahan ke database');

        $io->write(['<fg=cyan>Source File:</>', "<fg=yellow>{$this->filepath}</>"]);
        $io->write(['<fg=cyan>Total Items Post:</>', "<fg=yellow>{$collection->count()}</>"]);

        $io->newLine();

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
    }

    private function process(): void
    {

        $io = $this->io;
        $collection = $this->collection;

        // Task
        $io->newLine();
        $io->section("Start Insert Post Page: {$collection->count()} items");
        foreach ($collection->getIterator() as $index => $post) {
        }
    }
}
