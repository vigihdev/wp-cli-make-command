<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

class UniqueException extends WpCliMakeException
{

    public static function duplicate(string $field, mixed $value): self
    {
        return new self(
            message: sprintf('%s with value "%s" already exists', $field, $value),
            code: 409,
            solutions: [
                'Check if the value is already used',
                'Use a different value',
            ],
        );
    }
}
