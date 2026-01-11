<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use Exception;

abstract class WpCliMakeException extends Exception
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
        $this->context   = $context;
        $this->solutions = $solutions;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getSolutions(): array
    {
        return $this->solutions;
    }

    /**
     * Get a formatted error message with context
     */
    public function getFormattedMessage(): string
    {
        $message = $this->getMessage();

        if (!empty($this->context)) {
            $contextStr = json_encode($this->context, JSON_UNESCAPED_SLASHES);
            $message .= " (context: {$contextStr})";
        }
        return $message;
    }

}
