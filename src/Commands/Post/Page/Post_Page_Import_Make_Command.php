<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\Page;

use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;
use Vigihdev\WpCliModels\Entities\PostEntity;
use WP_CLI\Utils;

final class Post_Page_Import_Make_Command extends Base_Post_Command
{

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
        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Post Page");
        $io->note('Tidak ada perubahan ke database');

        $io->write(['<fg=cyan>Source File:</>', "<fg=yellow>{$this->filepath}</>"]);
    }

    /**
     * Process the post creation. 
     *
     * @return void 
     */
    private function process(): void
    {
        $io = $this->io;
        $io->title("📝 Insert Post Page");
        return;
        // Insert post
        $insert = PostEntity::create($this->postData);
        if (is_wp_error($insert)) {
            $io->errorBlock("Error insert post: " . $insert->get_error_message());
            return;
        }
        $io->successBlock("Post created successfully with ID: $insert");
    }
}
