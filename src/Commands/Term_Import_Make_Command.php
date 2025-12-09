<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliMake\Contracts\TermInterface;
use Vigihdev\WpCliMake\DTOs\TermDto;
use Vigihdev\WpCliMake\Utils\FilepathTransformerDto;
use WP_CLI;
use WP_CLI_Command;

final class Term_Import_Make_Command extends WP_CLI_Command
{
    private const ALLOW_EXTENSION = [
        'csv',
        'json'
    ];

    /**
     * Import terms from CSV or JSON file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to CSV or JSON file.
     *
     * ## EXAMPLES
     *
     *     wp make:term-import kota.csv
     *     wp make:term-import kota.json
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

        if (! in_array($ext, self::ALLOW_EXTENSION)) {
            $extString = implode(', ', self::ALLOW_EXTENSION);
            WP_CLI::error("Format file tidak didukung. Gunakan {$extString}.");
        }

        $items = [];
        if ($ext === 'csv') {
            $items = FilepathTransformerDto::fromFileCsv($file, TermDto::class);
        }

        if ($ext === 'json') {
            $itemDto = FilepathTransformerDto::fromFileJson($file, TermDto::class);
            $items = is_object($itemDto) ? [$itemDto] : $itemDto;
        }

        $this->import_terms($items, $taxonomy);
    }

    /**
     *
     * @param TermDto[] $items
     * @param string $taxonomy
     * @return void
     */
    private function import_terms(array $items, string $taxonomy)
    {
        $count = count($items);
        WP_CLI::log("📦 Mulai import {$count} term...");

        foreach ($items as $item) {
            if ($item instanceof TermInterface) {
                $name = trim($item->getName());
                $slug = $item->getSlug() ? sanitize_title($item->getSlug()) : sanitize_title($name);

                if (term_exists($name, $taxonomy)) {
                    WP_CLI::warning("Lewati: '{$name}' sudah ada.");
                    continue;
                }

                $result = wp_insert_term($name, $taxonomy, [
                    'slug' => $slug,
                    'description' => $item->getDescription(),
                    'parent' => $item->getParent(),
                ]);

                if (is_wp_error($result)) {
                    WP_CLI::warning("Gagal '{$name}': " . $result->get_error_message());
                    continue;
                }

                WP_CLI::success("Tambah term: {$name}");
            }
        }

        WP_CLI::success("🎉 Selesai import!");
    }
}
