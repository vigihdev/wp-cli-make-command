<?php

namespace Vigihdev\WpCliMake\Tests\Feature;


class CommandResult
{
    private string $output;

    public function __construct(string $output)
    {
        $this->output = $output;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getErrorOutput(): string
    {
        return $this->output; // Simplified
    }

    public function isSuccess(): bool
    {
        return str_contains($this->output, 'Success:');
    }
}
