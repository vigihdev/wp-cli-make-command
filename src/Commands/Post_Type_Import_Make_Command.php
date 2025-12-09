<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliModels\UI\CliStyle;
use WP_CLI;
use WP_CLI_Command;

final class Post_Type_Import_Make_Command extends WP_CLI_Command
{
    /**
     * @param array $args
     * @param array $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        $filepath = isset($args[0]) ? $args[0] : null;
        $io = new CliStyle();

        // Validasi file path
        if (!$filepath) {
            WP_CLI::error('❌ Path file harus disediakan.');
        }

        // Validasi file ada
        if (!file_exists($filepath)) {
            WP_CLI::error(
                sprintf('❌ File %s tidak ditemukan.', $io->textError($filepath))
            );
        }

        // Validasi file dapat dibaca
        if (!is_readable($filepath)) {
            WP_CLI::error(
                sprintf('❌ File %s tidak dapat dibaca.', $io->textError($filepath))
            );
        }

        WP_CLI::success(
            sprintf('Execute Command from class %s', self::class)
        );
    }
}
