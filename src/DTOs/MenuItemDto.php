<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\MenuItemInterface;

class MenuItemDto implements MenuItemInterface
{
    public function __construct(
        private string $title,
        private ?string $url = null,
        private int $parentId = 0,
        private ?string $target = null,
        private ?string $classes = null,
        private ?string $attrTitle = null,
        private ?string $type = 'custom',
        private int $objectId = 0,
        private ?string $objectType = null
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function getClasses(): ?string
    {
        return $this->classes;
    }

    public function getAttrTitle(): ?string
    {
        return $this->attrTitle;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }

    public function getObjectType(): ?string
    {
        return $this->objectType;
    }

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? $data['name'] ?? '',
            url: $data['url'] ?? $data['link'] ?? null,
            parentId: (int) ($data['parent_id'] ?? $data['parent'] ?? 0),
            target: $data['target'] ?? $data['link_target'] ?? null,
            classes: $data['classes'] ?? $data['css_classes'] ?? null,
            attrTitle: $data['attr_title'] ?? $data['title_attribute'] ?? null,
            type: $data['type'] ?? 'custom',
            objectId: (int) ($data['object_id'] ?? 0),
            objectType: $data['object_type'] ?? null
        );
    }

    /**
     * Validate DTO data
     */
    public function validate(): void
    {
        if (empty($this->title)) {
            throw new \InvalidArgumentException('Menu item title is required');
        }

        if ($this->type === 'custom' && empty($this->url)) {
            throw new \InvalidArgumentException('URL is required for custom menu items');
        }

        if (!in_array($this->type, ['custom', 'post_type', 'taxonomy', 'post_type_archive'])) {
            throw new \InvalidArgumentException('Invalid menu item type');
        }

        if ($this->parentId < 0) {
            throw new \InvalidArgumentException('Parent ID cannot be negative');
        }
    }

    /**
     * Get menu item data for wp_update_nav_menu_item
     */
    public function toArray(): array
    {
        return [
            'menu-item-title' => $this->title,
            'menu-item-url' => $this->url,
            'menu-item-parent-id' => $this->parentId,
            'menu-item-target' => $this->target,
            'menu-item-classes' => $this->classes,
            'menu-item-attr-title' => $this->attrTitle,
            'menu-item-type' => $this->type,
            'menu-item-object' => $this->objectType,
            'menu-item-object-id' => $this->objectId,
            'menu-item-status' => 'publish',
        ];
    }
}
