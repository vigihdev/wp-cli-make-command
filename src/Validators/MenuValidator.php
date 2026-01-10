<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\MenuException;
use Vigihdev\WpCliModels\Entities\MenuEntity;

final class MenuValidator
{
    public function __construct(
        private readonly string $name
    ) {
    }

    public static function validate(string $name): self
    {
        return new self($name);
    }

    public function validateCreate(): self
    {
        return $this->mustBeValidName()
            ->mustBeUnique();
    }

    public function mustBeValidName(): self
    {

        $name = trim($this->name);
        if (empty($name)) {
            throw MenuException::missingMenu($name);
        }

        if (preg_match('/[^a-z-A-Z-0-9\s]+/', $name)) {
            throw MenuException::invalidCharactersName($name);
        }

        if (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $name)) {
            throw MenuException::invalidCharactersName($name);
        }

        return $this;
    }

    public function mustBeUnique(): self
    {
        $menu = MenuEntity::get($this->name);
        if ($menu) {
            throw MenuException::duplicateName($this->name);
        }

        return $this;
    }
}
