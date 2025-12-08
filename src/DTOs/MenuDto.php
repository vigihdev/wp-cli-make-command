<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\MenuInterface;

final class MenuDto implements MenuInterface
{
    public function __construct(
        private string $name,
        private ?string $slug = null,
        private ?string $description = null,
        private ?string $location = null
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? $data['desc'] ?? null,
            location: $data['location'] ?? $data['theme_location'] ?? null
        );
    }

    /**
     * Validate DTO data
     */
    public function validate(): void
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException('Menu name is required');
        }

        if (strlen($this->name) > 200) {
            throw new \InvalidArgumentException('Menu name is too long (max 200 characters)');
        }
    }

    /**
     * Generate slug if not provided
     */
    public function generateSlug(): string
    {
        if ($this->slug) {
            return sanitize_title($this->slug);
        }

        return sanitize_title($this->name);
    }
}
