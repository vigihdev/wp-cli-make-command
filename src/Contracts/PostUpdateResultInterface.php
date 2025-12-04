<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;

use WP_Post;


interface PostUpdateResultInterface
{

    /**
     * Mendapatkan data post setelah operasi
     *
     * @return WP_Post|null Objek post atau null jika tidak ada
     */
    public function getPost(): ?WP_Post;

    /**
     * Mendapatkan informasi error jika terjadi kesalahan
     *
     * @return string|null Pesan error atau null jika tidak ada error
     */
    public function getError(): ?string;

    /**
     * Mendapatkan status apakah data telah diperbarui
     *
     * @return bool Status pembaruan data
     */
    public function isUpdated(): bool;
}
