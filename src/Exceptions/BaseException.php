<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use Throwable;

/**
 * Base exception dengan context data support
 */
abstract class BaseException extends \RuntimeException
{
    /**
     * @var array Additional context data
     */
    private array $context = [];

    /**
     * @param string $message Exception message
     * @param int $code Error code
     * @param array $context Additional context data
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        array $context = [],
        ?Throwable $previous = null
    ) {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get exception type (short class name)
     */
    public function getType(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

    /**
     * Convert to array for logging/reporting
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'context' => $this->getContext(),
            'trace' => $this->getTraceAsString(),
        ];
    }

    /**
     * Get formatted error message dengan context
     */
    public function getFormattedMessage(): string
    {
        $message = "[{$this->getType()}] {$this->getMessage()}";

        if (!empty($this->context)) {
            $contextStr = json_encode($this->context, JSON_PRETTY_PRINT);
            $message .= "\nContext: " . $contextStr;
        }

        return $message;
    }
}
