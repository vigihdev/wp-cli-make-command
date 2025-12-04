<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Utils;

use Vigihdev\WpCliMake\Exceptions\ContentFetchException;

final class ContentResolver
{
    public static function resolveFromFile(string $filepath): string
    {
        if (! is_file($filepath)) {
            throw ContentFetchException::fileNotFound($filepath);
        }

        try {
            $content = file_get_contents($filepath);

            if ($content === false) {
                throw new ContentFetchException(
                    message: "Failed to read file: {$filepath}",
                    code: 0,
                    context: ['path' => $filepath]
                );
            }

            return $content;
        } catch (\Error $e) {
            throw new ContentFetchException(
                message: "Error reading file: {$e->getMessage()}",
                code: 0,
                context: ['path' => $filepath, 'php_error' => $e->getMessage()],
                previous: $e
            );
        }
    }

    public static function resolveFromUrl(string $url): string
    {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw ContentFetchException::invalidUrl($url);
        }

        $response = wp_safe_remote_get($url, [
            'timeout' => 30,
            'user-agent' => 'WP-CLI-Make-Command/1.0',
        ]);

        if (is_wp_error($response)) {
            throw ContentFetchException::httpError(
                url: $url,
                statusCode: 0,
                error: $response->get_error_message()
            );
        }

        $statusCode = wp_remote_retrieve_response_code($response);

        if ($statusCode !== 200) {
            throw ContentFetchException::httpError(
                $url,
                $statusCode,
                wp_remote_retrieve_response_message($response)
            );
        }

        return wp_remote_retrieve_body($response);
    }
}
