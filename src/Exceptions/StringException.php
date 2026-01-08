<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class StringException extends WpCliMakeException
{
    protected array $context = [];

    protected array $solutions = [];

    /**
     * Get the context data for this exception
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get suggested solutions for this exception
     */
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

    /**
     * Create exception for string that is too short
     */
    public static function tooShort(int $min, string $actual = '', string $attribute = ''): self
    {
        return new self(
            message: sprintf('String %s is too short. Minimum length is %d characters', $attribute, $min),
            context: ['min' => $min, 'actual' => strlen($actual), 'attribute' => $attribute, 'value' => $actual],
            solutions: ['Extend the string to at least ' . $min . ' characters'],
        );
    }

    /**
     * Create exception for string that is too long
     */
    public static function tooLong(int $max, string $actual = ''): self
    {
        return new self(
            message: sprintf('String is too long. Maximum length is %d characters', $max),
            context: ['max' => $max, 'actual' => strlen($actual), 'value' => $actual],
            solutions: ['Shorten the string to maximum ' . $max . ' characters'],
        );
    }

    /**
     * Create exception for string that doesn't match expected value
     */
    public static function notEqual(string $expected, string $actual): self
    {
        return new self(
            message: sprintf('String does not match the expected value. Expected: %s', $expected),
            context: ['expected' => $expected, 'actual' => $actual],
            solutions: [
                'Ensure the string matches the expected format: ' . $expected,
                'Check for differences in case sensitivity or whitespace'
            ],
        );
    }

    /**
     * Create exception for string that doesn't match a pattern
     */
    public static function notMatch(string $pattern, string $actual): self
    {
        return new self(
            message: sprintf('String does not match the expected pattern. Expected: %s', $pattern),
            context: ['pattern' => $pattern, 'actual' => $actual],
            solutions: [
                'Ensure the string matches the pattern: ' . $pattern,
                'Check the string format'
            ],
        );
    }

    /**
     * Create exception for empty string when not allowed
     */
    public static function emptyNotAllowed(): self
    {
        return new self(
            message: 'String cannot be empty',
            context: [],
            solutions: [
                'Ensure the string has a value',
                'Provide a valid string value'
            ],
        );
    }

    /**
     * Create exception for string that contains invalid characters
     */
    public static function invalidCharacters(string $invalidChars, string $actual): self
    {
        return new self(
            message: sprintf('String contains invalid characters. Invalid: %s', $invalidChars),
            context: ['invalid_chars' => $invalidChars, 'actual' => $actual],
            solutions: [
                'Remove invalid characters: ' . $invalidChars,
                'Use only allowed characters'
            ],
        );
    }

    /**
     * Create exception for string that doesn't contain required characters
     */
    public static function missingRequiredCharacters(string $required, string $actual): self
    {
        return new self(
            message: sprintf('String does not contain required characters. Required: %s', $required),
            context: ['required' => $required, 'actual' => $actual],
            solutions: [
                'Add required characters: ' . $required,
                'Ensure the string contains all required characters'
            ],
        );
    }

    /**
     * Create exception for string that fails validation against a custom rule
     */
    public static function failedValidation(string $rule, string $actual): self
    {
        return new self(
            message: sprintf('String failed validation rule. Rule: %s', $rule),
            context: ['rule' => $rule, 'actual' => $actual],
            solutions: [
                'Review the validation rule: ' . $rule,
                'Ensure the string meets all requirements'
            ],
        );
    }

    /**
     * Create exception for string that is not in a list of allowed values
     */
    public static function notInList(array $allowed, string $actual): self
    {
        $allowedStr = implode(', ', $allowed);
        return new self(
            message: sprintf('String is not in the allowed list. Allowed: %s', $allowedStr),
            context: ['allowed' => $allowed, 'actual' => $actual],
            solutions: [
                'Use one of the following values: ' . $allowedStr,
                'Check if the value matches the allowed list'
            ],
        );
    }

    /**
     * Create exception for string that is not a valid email
     */
    public static function invalidEmail(string $email): self
    {
        return new self(
            message: sprintf('Invalid email address. Email: %s', $email),
            context: ['email' => $email],
            solutions: [
                'Ensure email format is correct (example@domain.com)',
                'Check if email contains @ and a valid domain'
            ],
        );
    }

    /**
     * Create exception for string that is not a valid URL
     */
    public static function invalidUrl(string $url): self
    {
        return new self(
            message: sprintf('Invalid URL. URL: %s', $url),
            context: ['url' => $url],
            solutions: [
                'Ensure URL has correct format (http:// or https://)',
                'Check if URL contains a valid domain'
            ],
        );
    }
}
