<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\TermInterface;


final class TermDto implements TermInterface
{
    public function __construct(
        private readonly string $taxonomy,
        private readonly string $term,
        private readonly string $slug,
        private readonly string $description,
    ) {}

    public function getTaxonomy(): string
    {
        return $this->taxonomy;
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
