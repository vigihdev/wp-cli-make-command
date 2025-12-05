<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\DTOs;

use Vigihdev\WpCliMake\Contracts\TermInterface;

/**
 * Class TermDto
 *
 * DTO untuk menyimpan dan mengakses data term WordPress
 */
final class TermDto implements TermInterface
{
    /**
     * Membuat instance objek TermDto dengan parameter yang ditentukan
     *
     * @param string $name Nama term WordPress
     * @param string $slug Slug term WordPress
     * @param string $description Deskripsi term WordPress
     * @param int $parent ID parent term WordPress
     */
    public function __construct(
        private readonly string $name,
        private readonly ?string $slug,
        private readonly ?string $description,
        private readonly int $parent = 0,
    ) {}

    /**
     * Mendapatkan nama term WordPress
     *
     * @return string Nama term
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Mendapatkan slug term WordPress
     *
     * @return string Slug term
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Mendapatkan deskripsi term WordPress
     *
     * @return string Deskripsi term
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Mendapatkan parent term WordPress
     *
     * @return int ID parent term
     */
    public function getParent(): int
    {
        return $this->parent;
    }
}
