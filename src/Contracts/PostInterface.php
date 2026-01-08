<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;

interface PostInterface
{

    public function getTitle(): string;

    public function getContent(): string;

    public function getType(): string;

    public function getCategory(): array;

    public function getTaxInput(): array;

    public function getMetaInput(): array;
}
