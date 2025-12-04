<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use WP_CLI;
use WP_CLI_Command;

final class Term_Make_Command extends WP_CLI_Command
{
    /**
     * Creates taxonomy term(s)
     *
     * ## OPTIONS
     *
     * <term>...
     * : Term name(s) to create (space-separated for bulk)
     *
     * --taxonomy=<taxonomy>
     * : Taxonomy slug (e.g., kota_category)
     *
     * [--slug=<slug>]
     * : Custom slug (only for single term)
     *
     * [--description=<description>]
     * : Term description (only for single term)
     *
     * [--parent=<parent>]
     * : Parent term slug/ID (only for single term)
     *
     * [--from-csv=<file>]
     * : Import terms from CSV file
     *
     * ## EXAMPLES
     *
     *     # Single term
     *     wp make:term Jakarta --taxonomy=kota_category
     *
     *     # Bulk terms
     *     wp make:term Jakarta Surabaya Bandung Medan --taxonomy=kota_category
     *
     *     # With custom slug
     *     wp make:term "Jakarta Pusat" --taxonomy=kota_category --slug=jakarta-pusat
     *
     *     # From CSV
     *     wp make:term --taxonomy=kota_category --from-csv=kota.csv
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args)
    {
        // Validate taxonomy
        $taxonomy = $assoc_args['taxonomy'] ?? null;

        if (!$taxonomy) {
            WP_CLI::error('--taxonomy parameter is required!');
            return;
        }

        if (!taxonomy_exists($taxonomy)) {
            WP_CLI::error("Taxonomy '$taxonomy' does not exist!");
            return;
        }

        // Check if import from CSV
        if (isset($assoc_args['from-csv'])) {
            $this->importFromCsv($assoc_args['from-csv'], $taxonomy);
            return;
        }

        // Validate terms
        if (empty($args)) {
            WP_CLI::error('At least one term name is required!');
            return;
        }

        $isBulk = count($args) > 1;
        $created = 0;
        $skipped = 0;
        $errors = 0;

        WP_CLI::line('Creating terms...');

        foreach ($args as $termName) {
            $termName = trim($termName);

            if (empty($termName)) {
                continue;
            }

            $termArgs = [
                'slug' => $isBulk ? sanitize_title($termName) : ($assoc_args['slug'] ?? null),
                'description' => !$isBulk ? ($assoc_args['description'] ?? '') : '',
            ];

            // Handle parent (only for single or when specified)
            if (!$isBulk && isset($assoc_args['parent'])) {
                $parent = $this->getParentId($assoc_args['parent'], $taxonomy);
                if ($parent) {
                    $termArgs['parent'] = $parent;
                }
            }

            // Check if term exists
            $existingTerm = term_exists($termName, $taxonomy);

            if ($existingTerm) {
                WP_CLI::warning("  - '$termName' already exists, skipped");
                $skipped++;
                continue;
            }

            // Create term
            $result = wp_insert_term($termName, $taxonomy, array_filter($termArgs));

            if (is_wp_error($result)) {
                WP_CLI::warning("  - Failed to create '$termName': " . $result->get_error_message());
                $errors++;
                continue;
            }

            WP_CLI::success("  - Created '$termName' (ID: {$result['term_id']})");
            $created++;
        }

        // Summary
        WP_CLI::line('');
        WP_CLI::line('Summary:');
        WP_CLI::line("  Created: $created");

        if ($skipped > 0) {
            WP_CLI::line("  Skipped: $skipped");
        }

        if ($errors > 0) {
            WP_CLI::line("  Errors:  $errors");
        }

        // Flush rewrite rules for CustomRewriteService
        flush_rewrite_rules();
        WP_CLI::success('Rewrite rules flushed!');
    }

    private function importFromCsv(string $file, string $taxonomy): void
    {
        if (!file_exists($file)) {
            WP_CLI::error("File not found: $file");
            return;
        }

        $handle = fopen($file, 'r');

        if (!$handle) {
            WP_CLI::error("Cannot open file: $file");
            return;
        }

        $header = fgetcsv($handle);
        $created = 0;
        $skipped = 0;

        WP_CLI::line('Importing from CSV...');

        while (($row = fgetcsv($handle)) !== false) {
            $termName = $row[0] ?? '';
            $slug = $row[1] ?? sanitize_title($termName);
            $description = $row[2] ?? '';

            if (empty($termName)) {
                continue;
            }

            if (term_exists($termName, $taxonomy)) {
                $skipped++;
                continue;
            }

            $result = wp_insert_term($termName, $taxonomy, [
                'slug' => $slug,
                'description' => $description,
            ]);

            if (!is_wp_error($result)) {
                $created++;
                WP_CLI::line("  - Imported: $termName");
            }
        }

        fclose($handle);

        WP_CLI::success("Imported $created terms, skipped $skipped");
        flush_rewrite_rules();
    }

    private function getParentId($parent, string $taxonomy): ?int
    {
        if (is_numeric($parent)) {
            return (int) $parent;
        }

        $parentTerm = get_term_by('slug', $parent, $taxonomy);

        return $parentTerm ? $parentTerm->term_id : null;
    }
}
