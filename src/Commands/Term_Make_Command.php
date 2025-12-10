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
        WP_CLI::success(
            sprintf('Execute Command from class %s', self::class)
        );
    }
}
