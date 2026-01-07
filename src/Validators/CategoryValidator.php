<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\CategoryException;

final class CategoryValidator
{
    public function __construct(
        private readonly int|string|null $term,
    ) {}


    public static function validate(int|string|null $term): self
    {
        return new self($term);
    }

    /**
     * Validate that the category exists in WordPress
     */
    public function mustExist(): self
    {
        $term = $this->term;

        if ($term === null) {
            throw CategoryException::notFound('');
        }

        // Check if it's a valid category by looking it up
        $categoryId = $this->getCategoryId($term);

        if ($categoryId === null) {
            throw CategoryException::notFound((string) $term);
        }

        return $this;
    }

    /**
     * Validate that the category name is valid according to WordPress standards
     */
    public function mustHaveValidName(): self
    {
        $term = $this->term;

        if ($term === null) {
            throw CategoryException::invalidName('');
        }

        if (is_int($term)) {
            // If it's an integer, it's likely a category ID, not a name
            throw CategoryException::invalidName((string) $term);
        }

        // Check length (WordPress term names should be reasonable length)
        if (strlen($term) < 1 || strlen($term) > 200) {
            throw CategoryException::invalidName($term);
        }

        return $this;
    }

    /**
     * Validate that the category doesn't already exist (for creation operations)
     */
    public function mustNotAlreadyExist(): self
    {
        $term = $this->term;

        if ($term === null) {
            throw CategoryException::alreadyExists('');
        }

        if (is_int($term)) {
            // If it's an integer, it's likely a category ID, so it already exists
            throw CategoryException::alreadyExists((string) $term);
        }

        // Check if a category with this name already exists
        $existingCategory = get_term_by('name', $term, 'category');

        if ($existingCategory) {
            throw CategoryException::alreadyExists($term);
        }

        return $this;
    }

    /**
     * Validate that the category slug is valid
     */
    public function mustHaveValidSlug(): self
    {
        $term = $this->term;

        if ($term === null) {
            throw CategoryException::invalidName('');
        }

        if (is_int($term)) {
            // If it's an integer, it's likely a category ID, not a slug
            return $this;
        }

        // Validate slug format if the term is being used as a slug
        if (!preg_match('/^[a-z0-9_-]+$/', sanitize_title($term))) {
            throw CategoryException::invalidName($term);
        }

        return $this;
    }

    /**
     * Validate that the parent category is valid
     */
    public function mustHaveValidParent(?int $parentId): self
    {
        $term = $this->term;

        if ($term === null) {
            throw CategoryException::invalidParent('', (string) ($parentId ?? 'null'));
        }

        if ($parentId === null) {
            // No parent to validate
            return $this;
        }

        // Check if the parent category exists
        $parentCategory = get_term($parentId, 'category');

        if (!$parentCategory || is_wp_error($parentCategory)) {
            throw CategoryException::invalidParent((string) $term, (string) $parentId);
        }

        return $this;
    }

    /**
     * Validate that the category is allowed for a specific post type
     */
    public function mustBeAllowedForPostType(string $postType): self
    {
        $term = $this->term;

        if ($term === null) {
            throw CategoryException::notAllowedForPost('', $postType);
        }

        // Check if the post type supports categories
        if (!post_type_supports($postType, 'categories') && !post_type_supports($postType, 'category')) {
            throw CategoryException::notAllowedForPost((string) $term, $postType);
        }

        return $this;
    }

    /**
     * Run all validations for creating a category
     */
    public function validateForCreation(): self
    {
        return $this
            ->mustHaveValidName()
            ->mustNotAlreadyExist();
    }

    /**
     * Run all validations for using an existing category
     */
    public function validateForUsage(): self
    {
        return $this
            ->mustExist();
    }

    /**
     * Helper method to get category ID from various input types
     */
    private function getCategoryId(int|string $term): ?int
    {
        if (is_int($term)) {
            // If it's already an integer, treat as ID
            $category = get_term($term, 'category');
            if (!$category || is_wp_error($category)) {
                return null;
            }
            return (int) $category->term_id;
        } else {
            // If it's a string, try to find by name or slug
            $category = get_term_by('name', $term, 'category');
            if (!$category) {
                $category = get_term_by('slug', $term, 'category');
            }

            if (!$category || is_wp_error($category)) {
                return null;
            }
            return (int) $category->term_id;
        }
    }
}
