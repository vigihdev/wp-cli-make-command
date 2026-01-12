<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\DateException;

class DateValidator
{

    public static function validate(string $field, ?string $value = null, string $format = DateException::DATE_TIME_FORMAT): void
    {

        if ($value === null) {
            throw DateException::emptyDate($field, $value);
        }

        $d = \DateTime::createFromFormat($format, $value);
        if (!$d || $d->format($format) !== $value) {
            throw DateException::invalidDateTime($field, $value);
        }
    }
}
