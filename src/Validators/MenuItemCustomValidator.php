<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Contracts\MenuItemCustomInterface;
use Vigihdev\WpCliMake\Exceptions\{MenuException, MenuItemCustomException};
use Vigihdev\WpCliModels\Entities\{MenuEntity};

final class MenuItemCustomValidator
{

    public function __construct(
        private readonly MenuItemCustomInterface $menuItem,
    ) {}

    public static function validate(MenuItemCustomInterface $menuItem): self
    {
        return new self($menuItem);
    }

    public function mustBeExistMenu(): self
    {
        $menuName = $this->menuItem->getMenu();
        if (trim($menuName) === '') {
            throw MenuException::missingMenu((string)$menuName);
        }

        $menu = MenuEntity::get($menuName);
        if (! $menu) {
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

        if (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $title)) {
            throw MenuItemCustomException::invalidLabel($title);
        }

        return $this;
    }

    public function mustBeValidUrl(): self
    {
        $url = $this->menuItem->getLink();
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw MenuItemCustomException::invalidUrl($url);
        }

        return $this;
    }
}
