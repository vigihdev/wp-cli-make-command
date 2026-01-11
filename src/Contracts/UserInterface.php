<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Contracts;

interface UserInterface
{
    public function password(): string;
    public function username(): string;
    public function email(): string;
    public function role(): string;
    public function displayName(): ?string;
    public function nickname(): ?string;
    public function firstName(): ?string;
    public function lastName(): ?string;
    public function description(): ?string;
}
