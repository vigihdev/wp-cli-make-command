<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliMake\Contracts\MenuItemInterface;
use Vigihdev\WpCliMake\DTOs\MenuItemDto;
use Vigihdev\WpCliModels\UI\Styler;
use Vigihdev\WpCliMake\Utils\FilepathTransformerDto;
use WP_CLI;
use WP_CLI_Command;

final class Menu_Item_Import_Make_Command extends WP_CLI_Command
{
    private const ALLOW_EXTENSION = [
        'csv',
        'json'
    ];

    /**
     * Membuat item menu WordPress dari file JSON atau CSV
     *
     * ## OPTIONS
     *
     * <file>
     * : Path ke file JSON atau CSV yang berisi konfigurasi item menu
     * 
     * [--dry-run]
     *  : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     # Membuat item menu dari file JSON
     *     wp make:menu-item-import menu-items.json
     * 
     *     wp make:menu-item-import menu-items.json --dry-run
     *
     *     # Membuat item menu dari file CSV
     *     wp make:menu-item-import menu-items.csv
     *     wp make:menu-item-import menu-items.csv --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args Argumen
     * @param array $assoc_args Argumen asosiatif
     * @return void
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
    }

    private function validate() {}

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
