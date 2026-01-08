<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;


final class TaxonomyException extends WpCliMakeException
{
    public static function notRegistered(string $taxonomy): self
    {
        return new self(
            message: sprintf("Taxonomy not registered: %s", $taxonomy),
            context: ['taxonomy' => $taxonomy],
            code: 404,
            solutions: [
                "Check if the taxonomy '{$taxonomy}' is properly registered in WordPress.",
                "Verify the taxonomy name spelling and ensure it's registered before use."
            ]
        );
    }

    public static function alreadyRegistered(string $taxonomy): self
    {
        return new self(
            message: sprintf("Taxonomy already registered: %s", $taxonomy),
            context: ['taxonomy' => $taxonomy],
            code: 409,
            solutions: [
                "Use a different taxonomy name.",
                "Check if the taxonomy is being registered multiple times."
            ]
        );
    }

    public static function invalidName(string $taxonomy): self
    {
        return new self(
            message: sprintf("Invalid taxonomy name: %s", $taxonomy),
            context: ['taxonomy' => $taxonomy],
            code: 400,
            solutions: [
                "Taxonomy names must be between 1 and 32 characters.",
                "Use only lowercase alphanumeric characters, underscores, or hyphens.",
                "Avoid using reserved WordPress terms."
            ]
        );
    }

    public static function notFound(string $taxonomy): self
    {
        return new self(
            message: sprintf("Taxonomy not found: %s", $taxonomy),
            context: ['taxonomy' => $taxonomy],
            code: 404,
            solutions: [
                "Verify the taxonomy name is correct.",
                "Check if the taxonomy is registered in WordPress."
            ]
        );
    }

    public static function invalidObjectTaxonomy(string $objectType, string $taxonomy): self
    {
        return new self(
            message: sprintf("Object type '%s' is not registered to taxonomy '%s'", $objectType, $taxonomy),
            context: ['objectType' => $objectType, 'taxonomy' => $taxonomy],
            code: 400,
            solutions: [
                "Register the object type '{$objectType}' with the taxonomy '{$taxonomy}'.",
                "Check if the object type and taxonomy are properly configured."
            ]
        );
    }

    public static function reservedName(string $taxonomy): self
    {
        return new self(
            message: sprintf("Taxonomy name '%s' is reserved by WordPress", $taxonomy),
            context: ['taxonomy' => $taxonomy],
            code: 400,
            solutions: [
                "Use a different taxonomy name that is not reserved by WordPress.",
                "Refer to WordPress documentation for reserved taxonomy names."
            ]
        );
    }

    public static function invalidArguments(string $taxonomy, string $issue): self
    {
        return new self(
            message: sprintf("Invalid arguments for taxonomy '%s': %s", $taxonomy, $issue),
            context: ['taxonomy' => $taxonomy, 'issue' => $issue],
            code: 400,
            solutions: [
                "Check the arguments passed when registering the taxonomy.",
                "Verify all required parameters are provided correctly."
            ]
        );
    }

    public static function registrationFailed(string $taxonomy, string $reason): self
    {
        return new self(
            message: sprintf("Failed to register taxonomy '%s': %s", $taxonomy, $reason),
            context: ['taxonomy' => $taxonomy, 'reason' => $reason],
            code: 500,
            solutions: [
                "Check the WordPress error logs for more details.",
                "Verify that all required parameters are provided correctly.",
                "Ensure the taxonomy name is valid and not already in use."
            ]
        );
    }
}
