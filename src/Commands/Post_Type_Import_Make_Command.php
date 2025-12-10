<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Symfony\Component\Filesystem\Path;
use Vigihdev\WpCliModels\Exceptions\FileException;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\Validators\Support\FileValidator;
use WP_CLI;
use WP_CLI\Utils;

final class Post_Type_Import_Make_Command extends Base_Command
{

    public function __construct()
    {
        parent::__construct(name: 'wp make:post-type-import');
    }

    private function validateFilePath(?string $filepath, CliStyle $io): void
    {
        if (!$filepath) {
            WP_CLI::error('❌ Path file harus disediakan.');
        }
    }

    private function normalizeFilePath(string $filepath): string
    {
        if (Path::isRelative($filepath)) {
            return Path::normalize(Path::join(getcwd() ?? '', $filepath));
        }

        return $filepath;
    }

    private function validateFile(string $filepath): void
    {
        $validator = new FileValidator($filepath);
        $validator
            ->mustBeFile()
            ->mustExist()
            ->mustBeJson()
            ->validateForImport();
    }

    private function handleFileError(FileException $e, CliStyle $io): void
    {
        $errorMsg = sprintf(
            "❌ %s\n   📁 %s\n   💡 %s",
            $e->getMessage(),
            $e->getFilePath(),
            $e->getSuggestion()
        );

        WP_CLI::error($errorMsg);
    }

    /**
     * Import Post Type dari file JSON.
     *
     * ## OPTIONS
     *
     * [<file>]
     * : Jalankan tanpa perubahan database.
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

        $filepath = isset($args[0]) ? $args[0] : null;
        $io = new CliStyle();

        $this->validateFilePath($filepath, $io);
        $filepath = $this->normalizeFilePath($filepath);
        $this->validateFile($filepath);

        $this->executeCommand($args, $assoc_args, $io);
    }

    protected function executeCommand(array $args, array $assoc_args, CliStyle $io): void
    {
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run');
        if ($dryRun) {
            return;
        }
    }
}
