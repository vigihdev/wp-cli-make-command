<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\BooleanException;

class BooleanValidator
{
    /**
     * Valid boolean string values
     */
    private const VALID_TRUE_VALUES = ['true', '1', 'yes', 'on', 'y'];
    private const VALID_FALSE_VALUES = ['false', '0', 'no', 'off', 'n'];

    /**
     * Validasi dan convert ke boolean PHP
     */
    public static function validate(string $field, mixed $value): bool
    {
        // Handle empty
        if ($value === '' || $value === null) {
            throw BooleanException::emptyBoolean($field);
        }

        // Jika sudah boolean, langsung return
        if (is_bool($value)) {
            return $value;
        }

        // Jika integer (0 atau 1)
        if (is_int($value)) {
            if ($value === 1) return true;
            if ($value === 0) return false;
            throw BooleanException::invalidBoolean($field, $value);
        }

        // Jika string, normalisasi
        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            if (in_array($normalized, self::VALID_TRUE_VALUES, true)) {
                return true;
            }

            if (in_array($normalized, self::VALID_FALSE_VALUES, true)) {
                return false;
            }
        }

        // Jika tidak valid
        throw BooleanException::invalidBoolean($field, $value);
    }

    /**
     * Validasi strict (hanya 'true'/'false' string)
     */
    public static function validateStrict(string $field, string $value): bool
    {
        if ($value === 'true') return true;
        if ($value === 'false') return false;

        throw BooleanException::invalidBoolean($field, $value);
    }

    /**
     * Validasi dengan default value jika empty
     */
    public static function validateWithDefault(string $field, mixed $value, bool $default = false): bool
    {
        try {
            return self::validate($field, $value);
        } catch (BooleanException) {
            return $default;
        }
    }
}
