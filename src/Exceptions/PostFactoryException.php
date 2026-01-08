<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class PostFactoryException extends WpCliMakeException
{

    protected array $context = [];

    protected array $solutions = [];

    public static function invalidId(int $id): static
    {
        return new self(
            message: sprintf('Post ID "%d" is not valid.', $id),
            context: ['id' => $id],
            code: 400,
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
            code: 400,
            solutions: [
                'Ensure the author ID is a valid number',
                'Check if the author exists before creating the post'
            ],
        );
    }

    public static function authorNotFound(int $authorId): static
    {
        return new self(
            message: sprintf('Author with ID "%d" not found.', $authorId),
            context: ['authorId' => $authorId],
            code: 404,
            solutions: [
                'Check if the author exists before creating the post'
            ],
        );
    }

    public static function mustBeUniqueAuthor(): static
    {
        return new self(
            message: 'Post author must be unique. Author with same ID already exists.',
            context: [],
            code: 409,
            solutions: [
                'Ensure the author ID is unique',
                'Check if the author exists before creating the post'
            ],
        );
    }

    public static function invalidDateFormat(): static
    {
        return new self(
            message: 'Post date not valid date format.',
            context: [],
            code: 400,
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
            code: 400,
            solutions: [
                'Add a title to the post data',
                'Ensure title field is not empty'
            ],
        );
    }

    public static function missingName(): static
    {
        return new self(
            message: 'Post must have a name.',
            context: [],
            code: 400,
            solutions: [
                'Add a name to the post data',
                'Ensure name field is not empty'
            ],
        );
    }

    public static function duplicatePostName(string $name): static
    {
        return new self(
            message: sprintf('Post name "%s" already exists.', $name),
            context: ['name' => $name],
            code: 409,
            solutions: [
                'Use a unique name for the post',
                'Check if the post already exists'
            ],
        );
    }

    public static function duplicatePostTitle(string $title, string $type): static
    {
        return new self(
            message: sprintf('Post title "%s" already exists as a "%s" post.', $title, $type),
            context: ['title' => $title, 'type' => $type],
            code: 409,
            solutions: [
                'Use a unique title for the post',
                'Check if the post already exists'
            ],
        );
    }

    public static function missingContent(): static
    {
        return new self(
            message: 'Post must have content.',
            context: [],
            code: 400,
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
            code: 400,
            solutions: [
                'Set a valid post status (draft, publish, pending, private)'
            ],
        );
    }

    public static function missingType(): static
    {
        return new self(
            message: 'Post must have a type.',
            context: [],
            code: 400,
            solutions: [
                'Add a type to the post data',
                'Ensure type field is not empty'
            ],
        );
    }

    public static function invalidStatus(): static
    {
        return new self(
            message: 'Post post_status not valid.',
            context: [],
            code: 400,
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
            code: 400,
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
            code: 400,
            solutions: [
                'Ensure the ID is a positive integer',
                'Check if the post exists before performing operations'
            ],
        );
    }
}
