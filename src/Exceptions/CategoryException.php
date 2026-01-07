<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;


final class CategoryException extends WpCliMakeException
{
    public static function notRegistered(string $category): self
    {
        return new self(
            message: sprintf("Category not registered: %s", $category),
            context: ['category' => $category],
            solutions: [
                "Check if the category '{$category}' is properly registered in WordPress.",
                "Verify the category name spelling and ensure it's registered before use."
            ]
        );
    }

    public static function notFound(string $category): self
    {
        return new self(
            message: sprintf("Category not found: %s", $category),
            context: ['category' => $category],
            solutions: [
                "Verify the category name is correct.",
                "Check if the category exists in WordPress."
            ]
        );
    }

    public static function invalidName(string $category): self
    {
        return new self(
            message: sprintf("Invalid category name: %s", $category),
            context: ['category' => $category],
            solutions: [
                "Category names must be between 1 and 32 characters.",
                "Use only lowercase alphanumeric characters, underscores, or hyphens.",
                "Avoid using reserved WordPress terms."
            ]
        );
    }

    public static function alreadyExists(string $category): self
    {
        return new self(
            message: sprintf("Category already exists: %s", $category),
            context: ['category' => $category],
            solutions: [
                "Use a different category name.",
                "Check if the category is being created multiple times."
            ]
        );
    }

    public static function invalidParent(string $category, string $parent): self
    {
        return new self(
            message: sprintf("Invalid parent category '%s' for category '%s'", $parent, $category),
            context: ['category' => $category, 'parent' => $parent],
            solutions: [
                "Verify the parent category exists and is valid.",
                "Check if the parent category is properly registered in WordPress."
            ]
        );
    }

    public static function creationFailed(string $category, string $reason): self
    {
        return new self(
            message: sprintf("Failed to create category '%s': %s", $category, $reason),
            context: ['category' => $category, 'reason' => $reason],
            solutions: [
                "Check the WordPress error logs for more details.",
                "Verify that all required parameters are provided correctly.",
                "Ensure the category name is valid and not already in use."
            ]
        );
    }

    public static function invalidArguments(string $category, string $issue): self
    {
        return new self(
            message: sprintf("Invalid arguments for category '%s': %s", $category, $issue),
            context: ['category' => $category, 'issue' => $issue],
            solutions: [
                "Check the arguments passed when creating the category.",
                "Verify all required parameters are provided correctly."
            ]
        );
    }

    public static function notAllowedForPost(string $category, string $postType): self
    {
        return new self(
            message: sprintf("Category '%s' is not allowed for post type '%s'", $category, $postType),
            context: ['category' => $category, 'postType' => $postType],
            solutions: [
                "Check if the post type supports categories.",
                "Verify that the category taxonomy is registered for the specified post type."
            ]
        );
    }
}
