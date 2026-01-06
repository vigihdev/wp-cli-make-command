<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts\Able;

interface JsonAbleInterface
{
    public function toJson(): string;
}
