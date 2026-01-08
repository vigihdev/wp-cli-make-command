<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class MenuException extends WpCliMakeException
{

    public static function notAllowMenuType(string $type): static
    {
        return new self(
            message: sprintf('Menu type "%s" is not allow.', $type),
            code: 400,
            context: ['type' => $type],
            solutions: [
                'Check the menu type in the code.',
                'Add the menu type to the allow list.',
            ]
        );
    }

    public static function notFoundMenuItemTerm(string $termId, string $taxonomy): static
    {
        return new self(
            message: sprintf('Menu item term with ID "%s" not found in taxonomy "%s".', $termId, $taxonomy),
            code: 404,
            context: [
                'term_id' => $termId,
                'taxonomy' => $taxonomy
            ],
            solutions: [
                'Check if the term ID exists in the specified taxonomy.',
                'Verify the taxonomy name is correct.',
                'Create the term if it does not exist.',
            ]
        );
    }
    
    public static function menuNotFound(string $menuId): static
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
    
    public static function invalidMenuItemData(array $data, string $reason): static
    {
        return new self(
            message: sprintf('Invalid menu item data: %s', $reason),
            code: 400,
            context: [
                'data' => $data,
                'reason' => $reason
            ],
            solutions: [
                'Check the required fields for the menu item.',
                'Ensure all mandatory fields are present and valid.',
                'Verify the format of the data being provided.',
            ]
        );
    }
    
    public static function failedToCreateMenuItem(string $menuId, string $reason): static
    {
        return new self(
            message: sprintf('Failed to create menu item in menu "%s": %s', $menuId, $reason),
            code: 500,
            context: [
                'menu_id' => $menuId,
                'reason' => $reason
            ],
            solutions: [
                'Check the WordPress error logs for more details.',
                'Verify that the menu exists and is accessible.',
                'Ensure all required parameters are provided correctly.',
            ]
        );
    }
    
    public static function menuTermAssociationFailed(string $termId, string $menuId): static
    {
        return new self(
            message: sprintf('Failed to associate term "%s" with menu "%s"', $termId, $menuId),
            code: 500,
            context: [
                'term_id' => $termId,
                'menu_id' => $menuId
            ],
            solutions: [
                'Check if the term and menu exist and are valid.',
                'Verify the relationship between the term and menu is properly configured.',
                'Review WordPress capabilities for menu management.',
            ]
        );
    }
    
    public static function invalidMenuRelationship(string $parentId, string $childId): static
    {
        return new self(
            message: sprintf('Invalid menu item relationship: parent "%s" and child "%s"', $parentId, $childId),
            code: 400,
            context: [
                'parent_id' => $parentId,
                'child_id' => $childId
            ],
            solutions: [
                'Verify the parent menu item exists.',
                'Check if the parent item is valid for creating a submenu.',
                'Ensure the parent-child relationship follows WordPress menu structure rules.',
            ]
        );
    }
}