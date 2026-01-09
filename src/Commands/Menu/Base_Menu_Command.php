<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu;

use Vigihdev\WpCliMake\Exceptions\{MakeHandlerException, MakeHandlerExceptionInterface};
use Vigihdev\WpCliModels\UI\WpCliStyle;
use WP_CLI_Command;

abstract class Base_Menu_Command extends WP_CLI_Command
{

    protected string $menu = '';

    protected string $filepath = '';

    protected WpCliStyle $io;

    protected MakeHandlerExceptionInterface $exceptionHandler;

    public function __construct(
        private readonly string $name
    ) {

        parent::__construct();

        $this->io = new WpCliStyle();
        $this->exceptionHandler = new MakeHandlerException();
    }

    public function __invoke(array $args, array $assoc_args) {}
}
