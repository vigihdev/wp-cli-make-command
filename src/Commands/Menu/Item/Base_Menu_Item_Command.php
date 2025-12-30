<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu\Item;

use Vigihdev\WpCliMake\Exceptions\{MakeHandlerException, MakeHandlerExceptionInterface};
use Vigihdev\WpCliModels\UI\WpCliStyle;
use WP_CLI_Command;

abstract class Base_Menu_Item_Command extends WP_CLI_Command
{

    protected string $filepath = '';

    protected WpCliStyle $io;

    protected MakeHandlerExceptionInterface $exceptionHandler;

    public function __construct(
        private readonly string $name
    ) {

        $this->io = new WpCliStyle();
        $this->exceptionHandler = new MakeHandlerException();
        parent::__construct();
    }
}
