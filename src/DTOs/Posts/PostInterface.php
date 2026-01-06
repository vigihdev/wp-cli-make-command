<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs\Posts;

interface PostInterface
{

    public function getTitle(): string;

    public function getContent(): string;

    public function getTaxInput(): array;
}
