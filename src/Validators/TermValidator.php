<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Contracts\TermInterface;
use Vigihdev\WpCliMake\Exceptions\TermException;

final class TermValidator
{
    public function __construct(
        private readonly TermInterface $term
    ) {}

    public static function validate(TermInterface $term): self
    {
        return new self($term);
    }

    /**
     * Pastikan taxonomy sudah terdaftar di WordPress
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
     * Pastikan term sudah ada di taxonomy
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
     * Pastikan slug valid (tidak kosong jika diisi)
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
     * Pastikan description valid
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
     * Pastikan term belum ada (untuk operasi create)
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
     * Validasi semua field term
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
