<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use Exception;

final class TermException extends Exception
{

    protected array $context = [];
    protected array $solutions = [];

    public function __construct(
        string $message,
        array $context = [],
        int $code = 0,
        \Throwable $previous = null,
        array $solutions = []
    ) {
        $this->context = $context;
        $this->solutions = $solutions;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getSolutions(): array
    {
        return $this->solutions;
    }

    public static function notFound(string $taxonomy, string $term): self
    {
        return new self(
            message: "Term {$term} not exist in taxonomy {$taxonomy}.",
            context: compact('taxonomy', 'term'),
            solutions: [
                "Create the term '{$term}' in taxonomy '{$taxonomy}' first."
            ]
        );
    }

    public static function invalidTaxonomy(string $taxonomy): self
    {
        return new self(
            message: "Taxonomy '{$taxonomy}' is not valid.",
            context: compact('taxonomy'),
            solutions: [
                "Check if the taxonomy '{$taxonomy}' is registered in WordPress.",
                "Verify the taxonomy name spelling."
            ]
        );
    }

    public static function alreadyExists(string $taxonomy, string $term, int $termId): self
    {
        return new self(
            message: "Term '{$term}' already exists in taxonomy '{$taxonomy}' with ID {$termId}.",
            context: compact('taxonomy', 'term', 'termId'),
            solutions: [
                "Use the existing term ID {$termId} instead of creating a new one.",
                "Choose a different term name."
            ]
        );
    }

    public static function creationFailed(string $taxonomy, string $term, string $reason): self
    {
        return new self(
            message: "Failed to create term '{$term}' in taxonomy '{$taxonomy}': {$reason}",
            context: compact('taxonomy', 'term', 'reason'),
            solutions: [
                "Check the WordPress error logs for more details.",
                "Verify that the taxonomy '{$taxonomy}' is properly registered.",
                "Ensure the term name is valid and not empty."
            ]
        );
    }

    public static function invalidTermData(array $data, string $issue): self
    {
        return new self(
            message: "Invalid term data: {$issue}.",
            context: compact('data', 'issue'),
            solutions: [
                "Check the required fields in the term data.",
                "Ensure all mandatory fields are present and valid."
            ]
        );
    }
}
