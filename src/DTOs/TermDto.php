<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\Able\ArrayAbleInterface;
use Vigihdev\WpCliMake\Contracts\TermInterface;


final class TermDto implements TermInterface, ArrayAbleInterface
{
    public function __construct(
        private readonly string $taxonomy,
        private readonly string $term,
        private readonly ?string $slug = null,
        private readonly ?string $description = null,
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

    public function toArray(): array
    {
        return array_filter([
            'name' => sanitize_text_field($this->getTerm()),
            'taxonomy' => $this->getTaxonomy(),
            'slug' => $this->getSlug() ?? sanitize_title($this->getTerm()),
            'description' => $this->getDescription(),
        ], function ($value) {
            return $value !== null;
        });
    }
}
