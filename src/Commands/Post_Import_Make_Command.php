<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use WP_CLI;
use WP_CLI_Command;

final class Post_Import_Make_Command extends WP_CLI_Command
{
    /**
     * ## OPTIONS
     *
     * <file>
     * : Path ke file JSON
     * 
     * [--dry-run]
     *  : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     wp make:post-import post.json
     * 
     *     wp make:post-import post-data.json --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        $filepath = isset($args[0]) ? $args[0] : null;

        // Validasi file path
        if (!$filepath) {
            WP_CLI::error('❌ Path file harus disediakan.');
        }

        // Validasi file ada
        if (!file_exists($filepath)) {
            WP_CLI::error(
                sprintf('❌ File "%s" tidak ditemukan.', WP_CLI::colorize("%Y{$filepath}%n"))
            );
        }

        // Validasi file dapat dibaca
        if (!is_readable($filepath)) {
            WP_CLI::error(
                sprintf('❌ File "%s" tidak dapat dibaca.', WP_CLI::colorize("%Y{$filepath}%n"))
            );
        }

        WP_CLI::success(
            sprintf('Execute Command from class %s', self::class)
        );
    }

    private function summary()
    {
        WP_CLI::log('📊 Ringkasan Impor:');
        WP_CLI::log(sprintf('  ✅ Berhasil: %d', 22));
        WP_CLI::log(sprintf('  ❌ Gagal:    %d', 0));
        WP_CLI::log(sprintf('  📦 Total:    %d', 2));
        WP_CLI::success('🎉 Selesai impor!');
        WP_CLI::error('Impor gagal! Tidak ada item yang berhasil diimpor.');
        WP_CLI::success(
            sprintf('Tambah item menu: %s', '')
        );
        WP_CLI::log(
            sprintf('📦 Mulai impor %d item menu...', 20)
        );
    }

    private function proccess() {}
}
