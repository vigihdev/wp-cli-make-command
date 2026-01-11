<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

class DateException extends WpCliMakeException
{

    public const DATE_FORMAT = 'Y-m-d';
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public static function invalidDate(string $field, string $value): self
    {
        return new self(
            message: sprintf("Date '%s' is not a valid date for field '%s'.", $value, $field),
            code: 400,
            context: [
                'field' => $field,
                'value' => $value,
            ],
            solutions: [
                "Check the date format. It should be 'Y-m-d' or 'Y-m-d H:i:s'.",
            ],
        );
    }

    public static function invalidDateTime(string $field, string $value): self
    {
        return new self(
            message: sprintf("Date '%s' is not a valid date time for field '%s'.", $value, $field),
            code: 400,
            context: [
                'field' => $field,
                'value' => $value,
            ],
            solutions: [
                "Check the date time format. It should be 'Y-m-d H:i:s'.",
            ],
        );
    }
}
