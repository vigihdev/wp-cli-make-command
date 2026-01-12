<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

class JsonException extends WpCliMakeException
{

    public static function emptyValue(string $field): self
    {
        return new self(
            message: sprintf("JSON for field '%s' cannot be empty.", $field),
            code: 400,
            context: [
                'field' => $field,
            ],
            solutions: [
                "Provide a valid JSON string.",
                "Refer to the JSON documentation for the correct format.",
                "Example: {\"key\": \"value\"} Or [{\"key\": \"value\"},{\"key2\": \"value2\"}]",
            ],
        );
    }

    public static function invalidJson(string $field, string $value): self
    {
        return new self(
            message: sprintf("JSON '%s' is not a valid JSON for field '%s'.", $value, $field),
            code: 400,
            context: [
                'field' => $field,
                'value' => $value,
            ],
            solutions: [
                "Check the JSON format. It should be valid JSON.",
                "Use a valid JSON string.",
                "Refer to the JSON documentation for the correct format.",
                "Example: {\"key\": \"value\"} Or [{\"key\": \"value\"},{\"key2\": \"value2\"}]",
            ],
        );
    }
}
