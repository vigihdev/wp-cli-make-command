<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

final class TermValidator
{
    public function __construct() {}

    public static function validate(): self
    {
        return new self();
    }
}
