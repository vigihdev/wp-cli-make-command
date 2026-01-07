<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\User;

use Vigihdev\WpCliModels\Contracts\Fields\FieldInterface;
use WP_CLI_Command;
use Vigihdev\WpCliModels\Exceptions\Handler\{HandlerExceptionInterface, WpCliExceptionHandler};

abstract class Base_User_Command extends WP_CLI_Command
{

    protected string $fields = '';

    protected FieldInterface $field;

    protected HandlerExceptionInterface $exceptionHandler;

    public function __construct(
        protected readonly string $name
    ) {
        parent::__construct();
        $this->exceptionHandler = new WpCliExceptionHandler();
    }
}
