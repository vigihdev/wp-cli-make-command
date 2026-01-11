<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;

interface TermInterface
{
    public function getTaxonomy(): string;

    public function getTerm(): string;

    public function getSlug(): ?string;

    public function getDescription(): ?string;
}
