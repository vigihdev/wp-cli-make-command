<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;

use WP_Post;

interface PostCreateResultInterface
{
    public function isCreated(): bool;
    public function getPost(): ?WP_Post;
    public function getError(): ?string;
    public function isDuplicate(): bool;
}
