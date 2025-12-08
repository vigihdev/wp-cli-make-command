<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliMake\Contracts\MenuItemInterface;
use Vigihdev\WpCliMake\DTOs\MenuItemDto;
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
     * <menu-id>
     * : ID atau slug menu tempat item akan ditambahkan
     *
     * ## EXAMPLES
     *
     *     # Membuat item menu dari file JSON
     *     wp make:menu-item-import menu-items.json 15
     *
     *     # Membuat item menu dari file CSV
     *     wp make:menu-item-import menu-items.csv main-menu
     *
     * @when after_wp_load
     * @param array $args Argumen posisi: [file, menu-id]
     * @param array $assoc_args Argumen asosiatif
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        $filepath = isset($args[0]) ? $args[0] : null;
        $menu_identifier = isset($args[1]) ? $args[1] : null;

        // Validasi file path
        if (!$filepath) {
            WP_CLI::error('Path file harus disediakan.');
            return;
        }

        // Validasi menu identifier
        // if (!$menu_identifier) {
        //     WP_CLI::error('ID atau slug menu harus disediakan.');
        //     return;
        // }

        // Validasi file ada
        if (!file_exists($filepath)) {
            WP_CLI::error(sprintf('File "%s" tidak ditemukan.', $filepath));
            return;
        }

        // Validasi file dapat dibaca
        if (!is_readable($filepath)) {
            WP_CLI::error(sprintf('File "%s" tidak dapat dibaca.', $filepath));
            return;
        }

        // // Dapatkan objek menu
        // $menu = null;
        // if (is_numeric($menu_identifier)) {
        //     $menu = wp_get_nav_menu_object((int) $menu_identifier);
        // } else {
        //     $menu = wp_get_nav_menu_object($menu_identifier);
        // }

        // if (!$menu) {
        //     WP_CLI::error(sprintf('Menu "%s" tidak ditemukan.', $menu_identifier));
        //     return;
        // }

        // // Periksa ekstensi file
        // $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        // if (!in_array($ext, self::ALLOW_EXTENSION)) {
        //     $extString = implode(', ', self::ALLOW_EXTENSION);
        //     WP_CLI::error(sprintf('Format file tidak didukung. Gunakan %s.', $extString));
        //     return;
        // }

        // // Proses file berdasarkan ekstensi
        // $items = [];
        // if ($ext === 'csv') {
        //     $items = FilepathTransformerDto::fromFileCsv($filepath, MenuItemDto::class);
        // }

        // if ($ext === 'json') {
        //     $itemDto = FilepathTransformerDto::fromFileJson($filepath, MenuItemDto::class);
        //     $items = is_object($itemDto) ? [$itemDto] : $itemDto;
        // }

        // // Impor item menu
        // $this->import_menu_items($items, $menu);
    }

    private function dryRun() {}
    private function proccess() {}

    /**
     * Impor item menu dari array objek MenuItemInterface
     *
     * @param MenuItemInterface[] $items Daftar item menu untuk diimpor
     * @param object $menu Objek menu WordPress
     * @return void
     */
    private function import_menu_items(array $items, object $menu): void
    {
        $count = count($items);
        WP_CLI::log(sprintf('📦 Mulai impor %d item menu...', $count));

        $success_count = 0;
        $error_count = 0;

        // foreach ($items as $item) {
        //     if ($item instanceof MenuItemInterface) {
        //         try {
        //             // Validasi item menu
        //             $item->validate();

        //             // Buat item menu
        //             $item_data = $item->toArray();
        //             $item_id = wp_update_nav_menu_item($menu->term_id, 0, $item_data);

        //             if (is_wp_error($item_id)) {
        //                 WP_CLI::warning(sprintf(
        //                     'Gagal menambahkan item "%s": %s',
        //                     $item->getTitle(),
        //                     $item_id->get_error_message()
        //                 ));
        //                 $error_count++;
        //                 continue;
        //             }

        //             WP_CLI::success(sprintf('Tambah item menu: %s', $item->getTitle()));
        //             $success_count++;
        //         } catch (\InvalidArgumentException $e) {
        //             WP_CLI::warning(sprintf(
        //                 'Item tidak valid "%s": %s',
        //                 $item->getTitle(),
        //                 $e->getMessage()
        //             ));
        //             $error_count++;
        //         } catch (\Throwable $e) {
        //             WP_CLI::warning(sprintf(
        //                 'Kesalahan saat memproses item "%s": %s',
        //                 $item->getTitle(),
        //                 $e->getMessage()
        //             ));
        //             $error_count++;
        //         }
        //     }
        // }

        // WP_CLI::log('');
        // WP_CLI::log('📊 Ringkasan Impor:');
        // WP_CLI::log(sprintf('  ✅ Berhasil: %d', $success_count));
        // WP_CLI::log(sprintf('  ❌ Gagal:    %d', $error_count));
        // WP_CLI::log(sprintf('  📦 Total:    %d', $count));

        // if ($success_count > 0) {
        //     WP_CLI::success('🎉 Selesai impor!');
        // } else {
        //     WP_CLI::error('Impor gagal! Tidak ada item yang berhasil diimpor.');
        // }
    }
}
