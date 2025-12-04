<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

/**
 * Exception untuk content fetching errors
 */
final class ContentFetchException extends BaseException
{
    public const CODE_FILE_NOT_FOUND = 1001;
    public const CODE_FILE_NOT_READABLE = 1002;
    public const CODE_INVALID_URL = 1003;
    public const CODE_HTTP_ERROR = 1004;
    public const CODE_STDIN_ERROR = 1005;
    public const CODE_GENERATION_FAILED = 1006;

    /**
     * Static factory methods untuk common cases
     */
    public static function fileNotFound(string $path): self
    {
        return new self(
            message: "File not found: {$path}",
            code: self::CODE_FILE_NOT_FOUND,
            context: ['path' => $path]
        );
    }

    public static function fileNotReadable(string $path): self
    {
        return new self(
            message: "File not readable: {$path}",
            code: self::CODE_FILE_NOT_READABLE,
            context: ['path' => $path]
        );
    }

    public static function invalidUrl(string $url): self
    {
        return new self(
            message: "Invalid URL: {$url}",
            code: self::CODE_INVALID_URL,
            context: ['url' => $url]
        );
    }

    public static function httpError(string $url, int $statusCode, string $error = ''): self
    {
        $message = "HTTP {$statusCode} error from {$url}";
        if ($error) {
            $message .= " - {$error}";
        }

        return new self(
            message: $message,
            code: self::CODE_HTTP_ERROR,
            context: ['url' => $url, 'status_code' => $statusCode, 'error' => $error]
        );
    }

    public static function stdinError(): self
    {
        return new self(
            message: "Failed to read from STDIN",
            code: self::CODE_STDIN_ERROR
        );
    }
}
