<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class StringException extends WpCliMakeException
{
    /**
     * Create exception for string that is too short
     */
    public static function tooShort(int $min, string $actual, string $field): self
    {
        return new self(
            message: sprintf('Field %s is too short. Minimum length is %d characters', $field, $min),
            context: [
                'min' => $min,
                'actual' => strlen($actual),
                'field' => $field,
                'value' => $actual
            ],
            code: 400,
            solutions: [
                sprintf('Extend the string %s to at least %d characters', $field, $min),
            ],
        );
    }

    /**
     * Create exception for string that is too long
     */
    public static function tooLong(int $max, string $actual, string $field): self
    {
        return new self(
            message: sprintf('Field %s is too long. Maximum length is %d characters', $field, $max),
            context: [
                'max' => $max,
                'actual' => strlen($actual),
                'field' => $field,
                'value' => $actual
            ],
            code: 400,
            solutions: [
                sprintf('Shorten the string %s to maximum %d characters', $field, $max),
            ],
        );
    }

    /**
     * Create exception for string that doesn't match expected value
     */
    public static function notEqual(string $expected, string $actual, string $field): self
    {
        return new self(
            message: sprintf('Field %s does not match the expected value. Expected: %s', $field, $expected),
            context: [
                'expected' => $expected,
                'actual' => $actual
            ],
            code: 400,
            solutions: [
                sprintf('Ensure the string %s matches the expected format: %s', $field, $expected),
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
            context: [
                'pattern' => $pattern,
                'actual' => $actual
            ],
            code: 400,
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
            code: 400,
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
            context: [
                'invalid_chars' => $invalidChars,
                'actual' => $actual
            ],
            code: 400,
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
            context: [
                'required' => $required,
                'actual' => $actual
            ],
            code: 400,
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
            context: [
                'rule' => $rule,
                'actual' => $actual
            ],
            code: 400,
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
            context: [
                'allowed' => $allowed,
                'actual' => $actual
            ],
            code: 400,
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
            context: [
                'email' => $email
            ],
            code: 400,
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
            context: [
                'url' => $url
            ],
            code: 400,
            solutions: [
                'Ensure URL has correct format (http:// or https://)',
                'Check if URL contains a valid domain'
            ],
        );
    }
}
