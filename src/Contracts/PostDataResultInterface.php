<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;


interface PostDataResultInterface
{

    public function getStatus(): bool;
    public function isValid(): bool;
    public function getMessage(): string;
}
