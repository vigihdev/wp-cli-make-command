<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliMake\Utils\FilepathResolver;
use WP_CLI;
use WP_CLI_Command;

final class Term_Import_Make_Command extends WP_CLI_Command
{
    /**
     * Import terms from CSV or JSON file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to CSV or JSON file.
     *
     * --taxonomy=<taxonomy>
     * : The taxonomy to import terms into.
     *
     * ## EXAMPLES
     *
     *     wp make:term-import kota.csv --taxonomy=kota_category
     *     wp make:term-import kota.json --taxonomy=kota_category
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args)
    {
        list($file) = $args;

        $taxonomy = $assoc_args['taxonomy'] ?? null;

        if (! $taxonomy) {
            WP_CLI::error("Parameter --taxonomy wajib diisi.");
        }

        if (! file_exists($file)) {
            WP_CLI::error("File tidak ditemukan: {$file}");
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            $items = FilepathResolver::fromFileCsv($file);
        } elseif ($ext === 'json') {
            $items = FilepathResolver::fromFileJson($file);
        } else {
            WP_CLI::error("Format file tidak didukung. Gunakan CSV atau JSON.");
        }

        $this->import_terms($items, $taxonomy);
    }


    private function import_terms($items, $taxonomy)
    {
        $count = count($items);
        WP_CLI::log("📦 Mulai import {$count} term...");

        foreach ($items as $item) {
            $name = trim($item['name']);
            $slug = $item['slug'] ? sanitize_title($item['slug']) : sanitize_title($name);

            if (term_exists($name, $taxonomy)) {
                WP_CLI::warning("Lewati: '{$name}' sudah ada.");
                continue;
            }

            $result = wp_insert_term($name, $taxonomy, [
                'slug' => $slug,
            ]);

            if (is_wp_error($result)) {
                WP_CLI::warning("Gagal '{$name}': " . $result->get_error_message());
                continue;
            }

            WP_CLI::success("Tambah term: {$name}");
        }

        WP_CLI::success("🎉 Selesai import!");
    }
}
