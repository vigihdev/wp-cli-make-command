<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\StringException;

final class StringValidator
{
    public function __construct(
        private readonly int|string|null $string,
        private readonly string $attribute = '',
    ) {}

    public static function validate(int|string|null $string, string $attribute = ''): self
    {
        return new self($string, $attribute);
    }

    /**
     * Validate that the string is not empty
     */
    public function notEmpty(): self
    {
        $string = $this->string;

        if ($string === null || $string === '') {
            throw StringException::emptyNotAllowed();
        }

        return $this;
    }

    /**
     * Validate that the string length is at least min characters
     */
    public function minLength(int $min): self
    {
        $string = (string) $this->string;
        $actualLength = strlen($string);

        if ($actualLength < $min) {
            throw StringException::tooShort($min, $string, $this->attribute);
        }

        return $this;
    }

    /**
     * Validate that the string length is at most max characters
     */
    public function maxLength(int $max): self
    {
        $string = (string) $this->string;
        $actualLength = strlen($string);

        if ($actualLength > $max) {
            throw StringException::tooLong($max, $string, $this->attribute);
        }

        return $this;
    }

    /**
     * Validate that the string length is between min and max characters
     */
    public function lengthBetween(int $min, int $max): self
    {
        $this->minLength($min);
        $this->maxLength($max);

        return $this;
    }

    /**
     * Validate that the string matches the expected value
     */
    public function equals(string $expected): self
    {
        $string = (string) $this->string;

        if ($string !== $expected) {
            throw StringException::notEqual($expected, $string);
        }

        return $this;
    }

    /**
     * Validate that the string matches a regex pattern
     */
    public function matches(string $pattern): self
    {
        $string = (string) $this->string;

        if (!preg_match($pattern, $string)) {
            throw StringException::notMatch($pattern, $string);
        }

        return $this;
    }

    /**
     * Validate that the string contains only alphanumeric characters
     */
    public function alphanumeric(): self
    {
        $string = (string) $this->string;
        $pattern = '/^[a-zA-Z0-9]+$/';

        if (!preg_match($pattern, $string)) {
            throw StringException::invalidCharacters('Non-alphanumeric characters', $string);
        }

        return $this;
    }

    /**
     * Validate that the string contains only alphabetic characters
     */
    public function alphabetic(): self
    {
        $string = (string) $this->string;
        $pattern = '/^[a-zA-Z]+$/';

        if (!preg_match($pattern, $string)) {
            throw StringException::invalidCharacters('Non-alphabetic characters', $string);
        }

        return $this;
    }

    /**
     * Validate that the string is a valid email
     */
    public function email(): self
    {
        $string = (string) $this->string;

        if (!filter_var($string, FILTER_VALIDATE_EMAIL)) {
            throw StringException::invalidEmail($string);
        }

        return $this;
    }

    /**
     * Validate that the string is a valid URL
     */
    public function url(): self
    {
        $string = (string) $this->string;

        if (!filter_var($string, FILTER_VALIDATE_URL)) {
            throw StringException::invalidUrl($string);
        }

        return $this;
    }

    /**
     * Validate that the string is in a list of allowed values
     */
    public function inList(array $allowed): self
    {
        $string = (string) $this->string;

        if (!in_array($string, $allowed, true)) {
            throw StringException::notInList($allowed, $string);
        }

        return $this;
    }

    /**
     * Validate that the string passes a custom validation rule
     */
    public function custom(callable $rule, string $ruleDescription = 'custom rule'): self
    {
        $string = (string) $this->string;

        if (!$rule($string)) {
            throw StringException::failedValidation($ruleDescription, $string);
        }

        return $this;
    }

    /**
     * Get the validated string value
     */
    public function getValue(): string
    {
        return (string) $this->string;
    }
}
