<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use Exception;

final class PostTypeException extends WpCliMakeException
{

    public static function notRegisteredPostType(string $postType): self
    {
        return new self(
            message: sprintf("Post type not registered: %s", $postType),
            context: ['postType' => $postType],
        );
    }

    public static function notRegisteredTaxonomies(string $taxonomy): self
    {
        return new self(
            message: sprintf("Taxonomy not registered: %s", $taxonomy),
            context: ['taxonomy' => $taxonomy],
        );
    }

    public static function notAllowTaxonomiesForPostType(string $postType, string $taxonomy): self
    {
        return new self(
            message: sprintf("Taxonomy '%s' not allowed for post type '%s'", $postType, $taxonomy),
            context: ['taxonomy' => $taxonomy, 'postType' => $postType],
        );
    }

    public static function notFoundTermInTaxonomy(string $term, string $taxonomy): self
    {
        return new self(
            message: sprintf("Term '%s' not exists in taxonomy '%s'", $term, $taxonomy),
            context: ['term' => $term, 'taxonomy' => $taxonomy],
        );
    }
}
