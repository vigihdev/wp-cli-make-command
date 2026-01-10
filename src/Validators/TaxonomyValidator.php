<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\TaxonomyException;

final class TaxonomyValidator
{
    public function __construct(
        private readonly int|string|null $taxonomy,
    ) {
    }

    public static function validate(int|string|null $taxonomy): self
    {
        return new self($taxonomy);
    }

    /**
     * Validate that the taxonomy exists in WordPress
     */
    public function mustExist(): self
    {
        $taxonomy = $this->taxonomy;

        if ($taxonomy === null) {
            throw TaxonomyException::notFound('');
        }

        if (is_int($taxonomy)) {
            // If it's an integer, it might be a term ID, so we need to get the taxonomy from the term
            $term = get_term($taxonomy);
            if (!$term || is_wp_error($term)) {
                throw TaxonomyException::notFound((string) $taxonomy);
            }
            $taxonomy = $term->taxonomy;
        }

        if (!taxonomy_exists($taxonomy)) {
            throw TaxonomyException::notFound($taxonomy);
        }

        return $this;
    }

    /**
     * Validate that the taxonomy is properly registered in WordPress
     */
    public function mustBeRegistered(): self
    {
        $taxonomy = $this->taxonomy;

        if ($taxonomy === null) {
            throw TaxonomyException::notRegistered('');
        }

        if (is_int($taxonomy)) {
            // If it's an integer, it might be a term ID, so we need to get the taxonomy from the term
            $term = get_term($taxonomy);
            if (!$term || is_wp_error($term)) {
                throw TaxonomyException::notRegistered((string) $taxonomy);
            }
            $taxonomy = $term->taxonomy;
        }

        if (!taxonomy_exists($taxonomy)) {
            throw TaxonomyException::notRegistered($taxonomy);
        }

        return $this;
    }

    /**
     * Validate that the taxonomy name is valid according to WordPress standards
     */
    public function mustHaveValidName(): self
    {
        $taxonomy = $this->taxonomy;

        if ($taxonomy === null) {
            throw TaxonomyException::invalidName('');
        }

        if (is_int($taxonomy)) {
            // If it's an integer, it's likely a term ID, not a taxonomy name
            throw TaxonomyException::invalidName((string) $taxonomy);
        }

        // Check length (WordPress taxonomy names must be between 1 and 32 characters)
        if (strlen($taxonomy) < 1 || strlen($taxonomy) > 32) {
            throw TaxonomyException::invalidName($taxonomy);
        }

        // Check format (only lowercase alphanumeric, underscores, hyphens)
        if (!preg_match('/^[a-z0-9_-]+$/', $taxonomy)) {
            throw TaxonomyException::invalidName($taxonomy);
        }

        // Check for reserved names
        $reservedNames = [
            'category', 'post_tag', 'nav_menu', 'link_category', 'post_format',
            'action', 'author', 'year', 'monthnum', 'day', 'post', 'page', 'comment',
            'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset'
        ];

        if (in_array($taxonomy, $reservedNames, true)) {
            throw TaxonomyException::reservedName($taxonomy);
        }

        return $this;
    }

    /**
     * Validate that the taxonomy is not already registered (useful for registration operations)
     */
    public function mustNotBeAlreadyRegistered(): self
    {
        $taxonomy = $this->taxonomy;

        if ($taxonomy === null) {
            throw TaxonomyException::alreadyRegistered('');
        }

        if (is_int($taxonomy)) {
            // If it's an integer, it's likely a term ID, not a taxonomy name
            throw TaxonomyException::alreadyRegistered((string) $taxonomy);
        }

        if (taxonomy_exists($taxonomy)) {
            throw TaxonomyException::alreadyRegistered($taxonomy);
        }

        return $this;
    }

    /**
     * Run all validations
     */
    public function validateAll(): self
    {
        return $this
            ->mustHaveValidName()
            ->mustBeRegistered();
    }
}
