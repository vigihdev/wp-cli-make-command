<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;

interface MenuInterface
{
    public function getName(): string;
    public function getSlug(): ?string;
    public function getDescription(): ?string;
    public function getLocation(): ?string;
}
