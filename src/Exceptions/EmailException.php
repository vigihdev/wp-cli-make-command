<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

class EmailException extends WpCliMakeException
{

    public static function emptyValue(string $field): self
    {
        return new self(
            message: sprintf("Email for field '%s' cannot be empty.", $field),
            code: 400,
            context: [
                'field' => $field,
            ],
            solutions: [
                "Provide a valid email address.",
            ],
        );
    }

    public static function duplicateEmail(string $email): self
    {
        return new self(
            message: sprintf("Email '%s' already exists.", $email),
            code: 400,
            context: [
                'email' => $email,
            ],
            solutions: [
                "Use a different email address.",
                "Check the email format. It should be 'example@domain.com'.",
            ],
        );
    }

    public static function invalidEmail(string $field, string $value): self
    {
        return new self(
            message: sprintf("Email '%s' is not a valid email for field '%s'.", $value, $field),
            code: 400,
            context: [
                'field' => $field,
                'value' => $value,
            ],
            solutions: [
                "Check the email format. It should be 'example@domain.com'.",
            ],
        );
    }
}
