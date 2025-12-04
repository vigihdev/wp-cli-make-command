<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use Throwable;

/**
 * Exception untuk validation errors
 */
final class ValidationException extends BaseException
{
    public const CODE_REQUIRED_FIELD = 2001;
    public const CODE_INVALID_FORMAT = 2002;
    public const CODE_OUT_OF_RANGE = 2003;
    public const CODE_INVALID_TYPE = 2004;
    public const CODE_DUPLICATE = 2005;

    /**
     * @var array Validation errors
     */
    private array $errors = [];

    public function __construct(
        string $message = '',
        int $code = 0,
        array $errors = [],
        array $context = [],
        ?Throwable $previous = null
    ) {
        $this->errors = $errors;
        parent::__construct($message, $code, $context, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function requiredField(string $fieldName): self
    {
        return new self(
            message: "Required field missing: {$fieldName}",
            code: self::CODE_REQUIRED_FIELD,
            errors: [],
            context: ['field' => $fieldName]
        );
    }

    public static function duplicate(string $fieldName, $value): self
    {
        return new self(
            message: "Duplicate {$fieldName} found: {$value}",
            code: self::CODE_DUPLICATE,
            errors: [],
            context: ['field' => $fieldName, 'value' => $value]
        );
    }

    public static function fromArray(array $errors): self
    {
        return new self(
            message: 'Validation failed',
            code: self::CODE_INVALID_FORMAT,
            errors: $errors,
            context: ['error_count' => count($errors)]
        );
    }
}
