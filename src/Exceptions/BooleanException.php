<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

class BooleanException extends WpCliMakeException
{

    public const VALID_VALUES = ['true', 'false', '1', '0', 'yes', 'no', 'on', 'off'];

    public static function invalidBoolean(string $field, mixed $value): self
    {
        return new self(
            message: sprintf(
                "Value '%s' is not a valid boolean for field '%s'.",
                self::stringifyValue($value),
                $field
            ),
            code: 400,
            context: [
                'field' => $field,
                'value' => $value,
                'value_type' => gettype($value),
            ],
            solutions: [
                "Accepted boolean values: 'true', 'false', '1', '0', 'yes', 'no', 'on', 'off'",
                "For string input, use lowercase: 'true' or 'false'",
                "For integer input, use: 1 (true) or 0 (false)",
            ],
        );
    }

    public static function emptyBoolean(string $field): self
    {
        return new self(
            message: sprintf("Boolean field '%s' cannot be empty.", $field),
            code: 400,
            context: ['field' => $field],
            solutions: [
                "Provide a boolean value for field '$field'",
                "Use 'true' or 'false' (string) or 1/0 (integer)",
            ],
        );
    }

    private static function stringifyValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return (string) $value;
        }

        return gettype($value);
    }
}
