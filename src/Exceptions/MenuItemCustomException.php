<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class MenuItemCustomException extends WpCliMakeException
{

    public static function invalidLabel(string $label): static
    {
        return new self(
            message: sprintf('Menu item label "%s" is invalid.', $label),
            code: 400,
            context: ['label' => $label],
            solutions: [
                'Check the label format and ensure it is valid.',
                'Verify the label does not contain any special characters.',
            ]
        );
    }

    public static function duplicateLabel(string $label): static
    {
        return new self(
            message: sprintf('A menu item with label "%s" already exists.', $label),
            code: 409,
            context: ['label' => $label],
            solutions: [
                'Use a different label for the new menu item.',
                'Find and update the existing menu item instead.',
                'Check if the label was mistyped or already in use.',
            ]
        );
    }

    public static function missingLabel(): static
    {
        return new self(
            message: 'Menu item label is required.',
            code: 400,
            context: [],
            solutions: [
                'Add a label to the menu item.',
                'Verify the label is not empty.',
            ]
        );
    }

    public static function duplicateUrl(string $url): static
    {
        return new self(
            message: sprintf('A menu item with URL "%s" already exists.', $url),
            code: 409,
            context: ['url' => $url],
            solutions: [
                'Use a different URL for the new menu item.',
                'Find and update the existing menu item instead.',
                'Check if the URL was mistyped or already in use.',
            ]
        );
    }

    public static function missingUrl(): static
    {
        return new self(
            message: 'Menu item URL is required.',
            code: 400,
            context: [],
            solutions: [
                'Add a URL to the menu item.',
                'Verify the URL is valid and not empty.',
            ]
        );
    }

    public static function invalidUrl(string $url): static
    {
        return new self(
            message: sprintf('Menu item URL "%s" is invalid.', $url),
            code: 400,
            context: ['url' => $url],
            solutions: [
                'Check the URL format and ensure it is valid.',
                'Verify the URL does not contain any special characters.',
            ]
        );
    }
}
