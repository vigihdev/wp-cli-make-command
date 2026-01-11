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

    public static function cannotBeEmpty(string $field): static
    {
        return new self(
            message: sprintf('Field "%s" cannot be empty.', $field),
            context: ['field' => $field],
            code: 400,
            solutions: [
                sprintf('Provide a value for field "%s".', $field),
                'Check if the field is required'
            ],
        );
    }

    public static function mustBeNumeric(string $field): static
    {
        return new self(
            message: sprintf('Field "%s" must be numeric.', $field),
            context: ['field' => $field],
            code: 400,
            solutions: [
                sprintf('Ensure the value for field "%s" is a valid number.', $field),
                'Check if the field is required'
            ],
        );
    }

    public static function mustBeString(string $field): static
    {
        return new self(
            message: sprintf('Field "%s" must be a string.', $field),
            context: ['field' => $field],
            code: 400,
            solutions: [
                sprintf('Ensure the value for field "%s" is a string.', $field),
                'Check if the field is required'
            ],
        );
    }

    public static function mustBeBoolean(string $field): static
    {
        return new self(
            message: sprintf('Field "%s" must be a boolean.', $field),
            context: ['field' => $field],
            code: 400,
            solutions: [
                sprintf('Ensure the value for field "%s" is a boolean.', $field),
                'Check if the field is required'
            ],
        );
    }

    public static function mustBeArray(string $field): static
    {
        return new self(
            message: sprintf('Field "%s" must be an array.', $field),
            context: ['field' => $field],
            code: 400,
            solutions: [
                sprintf('Ensure the value for field "%s" is an array.', $field),
                'Check if the field is required'
            ],
        );
    }

    public static function mustBeValidDate(string $field): static
    {
        return new self(
            message: sprintf('Field "%s" must be a valid date.', $field),
            context: ['field' => $field],
            code: 400,
            solutions: [
                sprintf('Ensure the value for field "%s" is a valid date.', $field),
                'Check if the field is required'
            ],
        );
    }

    public static function mustBeToLong(string $field, int $maxLength): static
    {
        return new self(
            message: sprintf('Field "%s" must be a string with a maximum length of %d characters.', $field, $maxLength),
            context: ['field' => $field, 'maxLength' => $maxLength],
            code: 400,
            solutions: [
                sprintf('Ensure the value for field "%s" is a string with a maximum length of %d characters.', $field, $maxLength),
                'Check if the field is required',
            ],
        );
    }

    public static function mustBeToShort(string $field, int $minLength): static
    {
        return new self(
            message: sprintf('Field "%s" must be a string with a minimum length of %d characters.', $field, $minLength),
            context: ['field' => $field, 'minLength' => $minLength],
            code: 400,
            solutions: [
                sprintf('Ensure the value for field "%s" is a string with a minimum length of %d characters.', $field, $minLength),
                'Check if the field is required',
            ],
        );
    }
}
