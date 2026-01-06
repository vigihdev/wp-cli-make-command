<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Entities;


final class TaxInputEntity
{

    public function __construct(
        private readonly array $taxInput,
    ) {}


    public function getTaxInput(): array
    {
        return $this->taxInput;
    }

    public function getKeys(): array
    {
        return array_keys($this->taxInput);
    }

    public function getValues(): array
    {
        return array_values($this->taxInput);
    }
}
