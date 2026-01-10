<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Support;

final class ImportSummary
{

    private int $success = 0;
    private int $failed = 0;
    private int $skipped = 0;

    /**
     * @param int $total
     */
    public function __construct(
        private readonly int $total
    ) {}

    public function addSuccess(): void
    {
        $this->success++;
    }

    public function addFailed(): void
    {
        $this->failed++;
    }

    public function addSkipped(): void
    {
        $this->skipped++;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get the summary results.
     * 
     * @return array{
     *     'Total Items': int,
     *     'Success': int,
     *     'Failed': int,
     *     'Skipped': int,
     * } The summary results.
     */
    public function getResults(): array
    {
        return [
            'Total Items' => $this->total,
            'Success' => $this->success,
            'Failed' => $this->failed,
            'Skipped' => $this->skipped,
        ];
    }
}
