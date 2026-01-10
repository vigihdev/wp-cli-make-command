<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class PostTypeException extends WpCliMakeException
{
    public static function invalidFormatTaxonomies(): self
    {
        return new self(
            message: 'Taxonomies must be provided as an array format',
            context: ['taxonomy' => 'not array'],
            code: 400,
            solutions: [
                'Ensure taxonomies are passed as an array (e.g., ["category", "post_tag"])',
            ],
        );
    }

    public static function emptyTaxonomies(): self
    {
        return new self(
            message: 'Taxonomies array cannot be empty',
            context: ['taxonomies' => 'empty array'],
            code: 400,
            solutions: [
                'Provide at least one valid taxonomy in the array',
            ],
        );
    }

    public static function notRegisteredPostType(string $postType): self
    {
        return new self(
            message: sprintf("Post type not registered: %s", $postType),
            context: ['postType' => $postType],
            code: 404,
            solutions: [
                "Check if the post type '{$postType}' is properly registered in WordPress.",
                "Verify the post type name spelling and ensure it's registered before use."
            ]
        );
    }

    public static function notRegisteredTaxonomies(string $taxonomy): self
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

    public static function notAllowTaxonomiesForPostType(string $postType, string $taxonomy): self
    {
        return new self(
            message: sprintf("Taxonomy '%s' not allowed for post type '%s'", $postType, $taxonomy),
            context: ['taxonomy' => $taxonomy, 'postType' => $postType],
            code: 403,
            solutions: [
                "Check if the post type supports the specified taxonomy.",
                "Verify that the taxonomy is registered for the specified post type."
            ]
        );
    }

    public static function notAllowPostType(string $postType): self
    {
        return new self(
            message: sprintf("Post type '%s' not allowed", $postType),
            context: ['postType' => $postType],
            code: 400,
            solutions: [
                "Post type '%s' not allowed. Please use another post type.",
            ]
        );
    }

    public static function notFoundTermInTaxonomy(string $term, string $taxonomy): self
    {
        return new self(
            message: sprintf("Term '%s' not exists in taxonomy '%s'", $term, $taxonomy),
            context: ['term' => $term, 'taxonomy' => $taxonomy],
            code: 404,
            solutions: [
                "Verify the term exists in the specified taxonomy.",
                "Check if the term name is spelled correctly."
            ]
        );
    }
}
