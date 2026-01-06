<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use Exception;

final class TermException extends Exception
{

    protected array $context = [];
    protected array $solutions = [];

    public function __construct(
        string $message,
        array $context = [],
        int $code = 0,
        \Throwable $previous = null,
        array $solutions = []
    ) {
        $this->context = $context;
        $this->solutions = $solutions;
        parent::__construct($message, $code, $previous);
    }
}
