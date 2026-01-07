<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class PostFactoryException extends \Exception
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

    public static function invalidId(int $id): static
    {
        return new self(
            message: sprintf('Post ID "%d" is not valid.', $id),
            context: ['id' => $id],
            solutions: [
                'Ensure the ID is a positive integer',
                'Check if the post exists before performing operations'
            ],
        );
    }

    public static function invalidAuthorFormat(): static
    {
        return new self(
            message: 'Post author must be a valid numeric ID.',
            context: [],
            solutions: [
                'Ensure the author ID is a valid number',
                'Check if the author exists before creating the post'
            ],
        );
    }

    public static function mustBeUniqueAuthor(): static
    {
        return new self(
            message: 'Post author must be unique. Author with same ID already exists.',
            context: [],
            solutions: [
                'Ensure the author ID is unique',
                'Check if the author exists before creating the post'
            ],
        );
    }

    public static function invalidDateFormat(): static
    {
        return new self(
            message: 'Post date must be a valid date format.',
            context: [],
            solutions: [
                'Use valid date format (e.g., Y-m-d H:i:s)',
                'Ensure date is not in the future unless intended'
            ],
        );
    }

    public static function missingTitle(): static
    {
        return new self(
            message: 'Post must have a title.',
            context: [],
            solutions: [
                'Add a title to the post data',
                'Ensure title field is not empty'
            ],
        );
    }

    public static function missingContent(): static
    {
        return new self(
            message: 'Post must have content.',
            context: [],
            solutions: [
                'Add content to the post data',
                'Ensure content field is not empty'
            ],
        );
    }

    public static function missingStatus(): static
    {
        return new self(
            message: 'Post must have a status.',
            context: [],
            solutions: [
                'Set a valid post status (draft, publish, pending, private)'
            ],
        );
    }

    public static function invalidStatus(): static
    {
        return new self(
            message: 'Post status must be valid.',
            context: [],
            solutions: [
                'Use one of the valid WordPress post statuses: draft, publish, pending, private, trash, auto-draft, inherit'
            ],
        );
    }

    public static function invalidDateGmtFormat(): static
    {
        return new self(
            message: 'Post date_gmt must be a valid GMT date format.',
            context: [],
            solutions: [
                'Use valid GMT date format (e.g., Y-m-d H:i:s)',
                'Ensure date_gmt is in UTC timezone'
            ],
        );
    }

    public static function invalidIdValue(int $id): static
    {
        return new self(
            message: sprintf('Post ID "%d" is not valid.', $id),
            context: ['id' => $id],
            solutions: [
                'Ensure the ID is a positive integer',
                'Check if the post exists before performing operations'
            ],
        );
    }
}
