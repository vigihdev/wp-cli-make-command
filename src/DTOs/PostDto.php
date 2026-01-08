<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\Able\ArrayAbleInterface;
use Vigihdev\WpCliMake\Contracts\PostInterface;

final class PostDto implements PostInterface, ArrayAbleInterface
{

    public function __construct(
        private readonly string $title,
        private readonly string $content,
        private readonly string $type,
        private readonly array $category = [],
        private readonly array $taxInput = [],
        private readonly array $metaInput = [],
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTaxInput(): array
    {
        return $this->taxInput;
    }

    public function getMetaInput(): array
    {
        return $this->metaInput;
    }

    public function getCategory(): array
    {
        return $this->category;
    }

    public function toArray(): array
    {
        return array_filter([
            'post_title' => sanitize_text_field($this->getTitle()),
            'post_content' => wp_kses_post($this->getContent()),
            'post_type' => $this->getType(),
            'tax_input' => $this->getTaxInput(),
            'meta_input' => $this->getMetaInput(),
            'post_category' => $this->getCategory(),
        ], function ($value) {
            return $value !== null;
        });
    }
}
