<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Contracts\PostInterface;
use Vigihdev\WpCliMake\Exceptions\PostTypeException;

final class PostTypeValidator
{
    public function __construct(
        private readonly PostInterface $post
    ) {}

    public static function validate(PostInterface $post): self
    {
        return new self($post);
    }

    public function validateCreate(): self
    {
        return $this->mustHaveRegisteredPostType()
            ->mustHaveRegisteredTaxonomies()
            ->mustAllowTaxonomiesForPostType()
            ->mustHaveExistingTerms();
    }

    public function mustHaveRegisteredPostType(): self
    {
        $type = $this->post->getType();
        if (! post_type_exists($type)) {
            throw PostTypeException::notRegisteredPostType($type);
        }

        return $this;
    }

    public function mustHaveRegisteredTaxonomies(): self
    {

        foreach ($this->post->getTaxInput() as $taxonomy => $_) {
            if (! taxonomy_exists($taxonomy)) {
                throw PostTypeException::notRegisteredTaxonomies($taxonomy);
            }
        }

        return $this;
    }

    /**
     * Pastikan taxonomy boleh dipakai oleh post type
     */
    public function mustAllowTaxonomiesForPostType(): self
    {
        $postType = $this->post->getType();
        foreach ($this->post->getTaxInput() as $taxonomy => $_) {
            if (! is_object_in_taxonomy($this->post->getType(), $taxonomy)) {
                throw PostTypeException::notAllowTaxonomiesForPostType($postType, $taxonomy);
            }
        }
        return $this;
    }

    /**
     * Pastikan term memang ada di taxonomy
     */
    public function mustHaveExistingTerms(): self
    {
        foreach ($this->post->getTaxInput() as $taxonomy => $terms) {
            foreach ($terms as $term) {
                if (! term_exists($term, $taxonomy)) {
                    throw PostTypeException::notFoundTermInTaxonomy($term, $taxonomy);
                }
            }
        }

        return $this;
    }
}
