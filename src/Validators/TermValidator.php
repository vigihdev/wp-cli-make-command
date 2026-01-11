<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Contracts\TermInterface;
use Vigihdev\WpCliMake\Exceptions\TermException;

/**
 * Validator for term data transfer objects.
 *
 * This validator ensures that the term data transfer object (DTO) meets the required criteria
 * for term creation or update operations in WordPress.
 */
final class TermValidator
{
    /**
     * Constructor for TermValidator.
     *
     * @param TermInterface $term The term data transfer object to validate.
     */
    public function __construct(
        private readonly TermInterface $term
    ) {
    }

    /**
     * Create a new instance of TermValidator.
     *
     * @param TermInterface $term The term data transfer object to validate.
     * @return self A new instance of TermValidator.
     */
    public static function validate(TermInterface $term): self
    {
        return new self($term);
    }

    /**
     * Validate that the taxonomy is registered in WordPress.
     *
     * @return self The current instance of TermValidator.
     * @throws TermException If the taxonomy is not registered.
     */
    public function mustHaveRegisteredTaxonomy(): self
    {
        $taxonomy = $this->term->getTaxonomy();
        if (! taxonomy_exists($taxonomy)) {
            throw TermException::invalidTaxonomy($taxonomy);
        }

        return $this;
    }

    /**
     * Validate that the term exists in the specified taxonomy.
     *
     * @return self The current instance of TermValidator.
     * @throws TermException If the term does not exist in the taxonomy.
     */
    public function mustHaveExistingTerm(): self
    {
        $taxonomy = $this->term->getTaxonomy();
        $term = $this->term->getTerm();

        if (! term_exists($term, $taxonomy)) {
            throw TermException::notFound($taxonomy, $term);
        }

        return $this;
    }
    /**
     * Validate that the term slug is valid.
     *
     * @return self The current instance of TermValidator.
     * @throws TermException If the slug is invalid.
     */
    public function mustHaveValidSlug(): self
    {
        $slug = $this->term->getSlug();

        if ($slug !== null && empty(trim($slug))) {
            throw TermException::invalidTermData(
                ['slug' => $slug],
                'Slug cannot be empty if provided'
            );
        }

        if ($slug !== null && !preg_match('/^[a-z0-9\-_]+$/', $slug)) {
            throw TermException::invalidTermData(
                ['slug' => $slug],
                'Slug must only contain lowercase letters, numbers, hyphens, and underscores'
            );
        }

        return $this;
    }

    /**
     * Validate that the term description is valid.
     *
     * @return self The current instance of TermValidator.
     * @throws TermException If the description is invalid.
     */
    public function mustHaveValidDescription(): self
    {
        $description = $this->term->getDescription();

        if ($description !== null && !is_string($description)) {
            throw TermException::invalidTermData(
                ['description' => $description],
                'Description must be a string'
            );
        }

        return $this;
    }

    /**
     * Validate that the term does not already exist in the specified taxonomy.
     *
     * @return self The current instance of TermValidator.
     * @throws TermException If the term already exists in the taxonomy.
     */
    public function mustNotAlreadyExists(): self
    {
        $taxonomy = $this->term->getTaxonomy();
        $term = $this->term->getTerm();

        $existingTerm = term_exists($term, $taxonomy);

        if ($existingTerm !== null) {
            $termId = is_array($existingTerm) ? $existingTerm['term_id'] : (int) $existingTerm;
            throw TermException::alreadyExists((string) $taxonomy, (string) $term, (int) $termId);
        }

        return $this;
    }

    /**
     * Validate all fields of the term for creation.
     *
     * @return self The current instance of TermValidator.
     * @throws TermException If any of the validation checks fail.
     */
    public function validateCreate(): self
    {
        return $this
            ->mustHaveRegisteredTaxonomy()
            ->mustHaveValidSlug()
            ->mustHaveValidDescription()
            ->mustNotAlreadyExists();
    }
}
