<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\DateException;

final class DateValidator
{

    public function __construct(
        private readonly string $field,
        private readonly ?string $value = null,
    ) {}

    public static function validate(string $field, ?string $value = null): self
    {
        return new self($field, $value);
    }

    public function notEmpty(): self
    {
        $value = $this->value;
        if ($value === null || trim((string)$value) === '') {
            throw DateException::emptyValue($this->field, $value);
        }
        return $this;
    }

    public function dateFormat(): self
    {
        if (!$this->validDate(DateException::DATE_FORMAT)) {
            throw DateException::invalidDate($this->field, $this->value);
        }
        return $this;
    }

    public function dateTimeFormat(): self
    {
        if (!$this->validDate()) {
            throw DateException::invalidDateTime($this->field, $this->value);
        }
        return $this;
    }

    private function validDate(string $format = DateException::DATE_TIME_FORMAT): bool
    {
        $d = \DateTime::createFromFormat($format, $this->value);
        return $d && $d->format($format) === $this->value;
    }
}
