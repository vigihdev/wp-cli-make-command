<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\{MenuException, MenuItemCustomException};
use Vigihdev\WpCliModels\Entities\{MenuEntity, MenuItemEntity};
use Vigihdev\WpCliModels\DTOs\Entities\Menu\MenuEntityDto;
use Vigihdev\WpCliModels\Contracts\Args\Menu\CustomItemMenuArgsInterface;

final class MenuItemCustomValidator
{
    private ?MenuEntityDto $menuEntity = null;

    public function __construct(
        private readonly CustomItemMenuArgsInterface $menuItem,
    ) {

        $menuName = $this->menuItem->getMenu();
        if ($this->menuEntity === null) {
            $this->menuEntity = MenuEntity::get((string)$menuName);
        }
    }

    public static function validate(CustomItemMenuArgsInterface $menuItem): self
    {
        return new self($menuItem);
    }

    public function validateCreate(): self
    {
        return $this->mustBeExistMenu()
            ->mustBeValidTitle()
            ->mustBeValidUrl()
            ->ensureValidParentIdIfDefined();
    }

    public function mustBeExistMenu(): self
    {
        $menuName = $this->menuItem->getMenu();
        if (trim($menuName) === '') {
            throw MenuException::missingMenu((string)$menuName);
        }

        if (! $this->menuEntity) {
            throw MenuException::notFound((string)$menuName);
        }

        return $this;
    }

    public function mustBeValidTitle(): self
    {
        $title = $this->menuItem->getTitle();
        if ($title === null || trim($title) === '') {
            throw MenuItemCustomException::missingLabel();
        }

        if (preg_match('/[^a-z-A-Z-0-9\s]+/', $title)) {
            throw MenuItemCustomException::invalidCharactersLabel($title);
        }

        if (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $title)) {
            throw MenuItemCustomException::invalidCharactersLabel($title);
        }

        if (strlen($title) > 50) {
            throw MenuItemCustomException::labelTooLong($title, 50);
        }

        $menu = $this->menuItem->getMenu();
        if (MenuItemEntity::existsByLabel($menu, $title)) {
            throw MenuItemCustomException::duplicateLabel($title);
        }

        return $this;
    }

    public function mustBeValidUrl(): self
    {
        $url = $this->menuItem->getLink();

        if (trim($url) === '') {
            throw MenuItemCustomException::missingUrl($url);
        }

        if (trim($url) === '#') {
            return $this;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw MenuItemCustomException::invalidUrl($url);
        }

        $menu = $this->menuItem->getMenu();
        if (MenuItemEntity::existsByUrl($menu, $url)) {
            throw MenuItemCustomException::duplicateUrl($url);
        }

        return $this;
    }

    public function ensureValidParentIdIfDefined(): self
    {

        $parentId = $this->menuItem->getParentId();
        if ($parentId === null) {
            return $this;
        }

        if (!is_numeric($parentId)) {
            throw MenuItemCustomException::notNumber('parent-id');
        }

        $post = get_post($parentId);
        if ($post === null) {
            throw MenuItemCustomException::notFoundParentId($parentId);
        }

        return $this;
    }
}
