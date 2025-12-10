<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\WpCliModels\UI\CliStyle;
use WP_CLI;
use WP_CLI_Command;

abstract class Base_Command extends WP_CLI_Command
{

    public function __construct(
        protected readonly string $name
    ) {
        return parent::__construct();
    }

    abstract protected function executeCommand(array $args, array $assoc_args, CliStyle $io): void;
}
