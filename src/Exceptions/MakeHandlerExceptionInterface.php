<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use Throwable;

interface MakeHandlerExceptionInterface
{
    public function handle(Throwable $e): void;
}
