<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts\Posts;

interface PostInterface
{

    public function getTitle(): string;

    public function getContent(): string;

    public function getType(): string;

    public function getTaxInput(): array;

    public function getMetaInput(): array;
}
