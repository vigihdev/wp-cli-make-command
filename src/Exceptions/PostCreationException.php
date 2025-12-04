<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use WP_Error;

/**
 * Exception untuk post creation/update failures
 */
final class PostCreationException extends BaseException
{
    public const CODE_INSERT_FAILED = 3001;
    public const CODE_UPDATE_FAILED = 3002;
    public const CODE_POST_NOT_FOUND = 3003;
    public const CODE_INVALID_AUTHOR = 3004;
    public const CODE_INVALID_STATUS = 3005;

    /**
     * Create dari WP_Error
     */
    public static function fromWpError(WP_Error $error, array $context = []): self
    {
        return new self(
            message: $error->get_error_message(),
            code: (int) $error->get_error_code(),
            context: [
                'wp_error_data' => $error->get_error_data(),
                'wp_error_codes' => $error->get_error_codes(),
            ],
            previous: null
        );
    }

    public static function insertFailed(array $postData, ?string $error = null): self
    {
        $message = 'Failed to insert post';
        if ($error) {
            $message .= ": {$error}";
        }

        // Remove sensitive data dari context
        $safePostData = array_filter($postData, function ($key) {
            return !in_array($key, ['post_password', 'post_author_email']);
        }, ARRAY_FILTER_USE_KEY);

        return new self(
            message: $message,
            code: self::CODE_INSERT_FAILED,
            context: ['post_data' => $safePostData]
        );
    }

    public static function postNotFound(int $postId): self
    {
        return new self(
            message: "Post with ID {$postId} not found",
            code: self::CODE_POST_NOT_FOUND,
            context: ['post_id' => $postId]
        );
    }
}
