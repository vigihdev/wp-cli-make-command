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
        WP_CLI::success(
            sprintf('Execute Command from class %s', self::class)
        );
    }
}
