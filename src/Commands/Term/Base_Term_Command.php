<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Term;

use Vigihdev\WpCliMake\Exceptions\{MakeHandlerExceptionInterface, MakeHandlerException};
use Vigihdev\WpCliModels\UI\WpCliStyle;
use WP_CLI_Command;

abstract class Base_Term_Command extends WP_CLI_Command
{

    protected string $fields = '';
    protected string $term;
    protected string $taxonomy;
    protected string $slug;
    protected string $description;

    protected MakeHandlerExceptionInterface $exceptionHandler;
    protected WpCliStyle $io;

    public function __construct(
        protected readonly string $name
    ) {
        parent::__construct();
        $this->exceptionHandler = new MakeHandlerException();
        $this->io = new WpCliStyle();
    }

    public function __invoke(array $args, array $assoc_args) {}
}
