<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;


/**
 * Interface PostDataResultInterface
 *
 * Interface untuk mendefinisikan struktur data hasil operasi post
 */
interface PostDataResultInterface
{
    public function isValid(): bool;
    public function getErrors(): array;

    /**
     * Mendapatkan pesan hasil operasi
     *
     * @return string Pesan hasil operasi
     */
    public function getMessage(): string;
}
