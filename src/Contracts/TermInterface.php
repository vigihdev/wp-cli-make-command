<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;

/**
 * Interface TermInterface
 *
 * Interface untuk mendefinisikan struktur data term WordPress
 */
interface TermInterface
{
    /**
     * Mendapatkan nama term WordPress
     *
     * @return string Nama term
     */
    public function getName(): string;

    /**
     * Mendapatkan slug term WordPress
     *
     * @return string Slug term
     */
    public function getSlug(): string;

    /**
     * Mendapatkan deskripsi term WordPress
     *
     * @return string Deskripsi term
     */
    public function getDescription(): string;

    /**
     * Mendapatkan parent term WordPress
     *
     * @return int ID parent term
     */
    public function getParent(): int;
}
