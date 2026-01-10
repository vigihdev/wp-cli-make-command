<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class MenuException extends WpCliMakeException
{
    public static function missingMenu(string $name): static
    {
        return new self(
            message: 'Menu name is required.',
            code: 400,
            context: ['name' => $name],
            solutions: [
                'Add a name for the new menu.',
                'Verify the menu name is not empty.',
            ]
        );
    }

    public static function duplicateName(string $name): static
    {
        return new self(
            message: sprintf('A menu with name "%s" already exists.', $name),
            code: 409,
            context: ['name' => $name],
            solutions: [
                'Use a different name for the new menu.',
                'Find and update the existing menu instead.',
                'Check if the name was mistyped or already in use.',
            ]
        );
    }

    public static function invalidCharactersName(string $name): static
    {
        return new self(
            message: sprintf('Menu name "%s" contains invalid characters.', $name),
            code: 400,
            context: ['name' => $name],
            solutions: [
                'Remove or replace any invalid characters from the menu name.',
                'Use only alphanumeric characters, hyphens, and underscores.',
                'Verify the menu name does not exceed the allowed length.',
            ]
        );
    }


    public static function notFound(string $menuId): static
    {
        return new self(
            message: sprintf('Menu with identifier "%s" not found.', $menuId),
            code: 404,
            context: ['menu_id' => $menuId],
            solutions: [
                'Check if the menu exists in WordPress.',
                'Verify the menu ID, name or slug is correct.',
                'Create the menu first before adding items to it.',
            ]
        );
    }
}
