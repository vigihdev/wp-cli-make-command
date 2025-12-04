<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Utils;

use WP_Error;
use Vigihdev\WpCliMake\Exceptions\ContentFetchException;

final class ContentResolver
{
    /**
     * Resolve content from various sources
     *
     * Priority order:
     * 1. Direct content string
     * 2. From file
     * 3. From URL
     * 4. From STDIN
     * 5. Generated content
     *
     * @throws ContentFetchException
     */
    public static function resolve(array $args, string $defaultContent = ''): string
    {
        // 1. Direct content
        if (isset($args['content']) && $args['content'] !== '') {
            return self::resolveDirectContent($args['content']);
        }

        // 2. From file
        if (isset($args['content-file']) && $args['content-file'] !== '') {
            return self::resolveFromFile($args['content-file']);
        }

        // 3. From URL
        if (isset($args['content-url']) && $args['content-url'] !== '') {
            return self::resolveFromUrl($args['content-url']);
        }

        // 4. Generated content
        if (isset($args['generate-content'])) {
            return self::generateContent($args['title'] ?? '');
        }

        return $defaultContent;
    }

    /**
     * Resolve direct content (string or STDIN)
     */
    private static function resolveDirectContent(string $content): string
    {
        // Check for STDIN indicator
        if ($content === '-') {
            return self::resolveFromStdin();
        }

        return $content;
    }

    /**
     * Read content from file
     *
     * @throws ContentFetchException
     */
    private static function resolveFromFile(string $filepath): string
    {
        if (!file_exists($filepath)) {
            throw new ContentFetchException("File not found: {$filepath}");
        }

        if (!is_readable($filepath)) {
            throw new ContentFetchException("File not readable: {$filepath}");
        }

        $content = file_get_contents($filepath);

        if ($content === false) {
            throw new ContentFetchException("Failed to read file: {$filepath}");
        }

        return $content;
    }

    /**
     * Fetch content from URL
     *
     * @throws ContentFetchException
     */
    private static function resolveFromUrl(string $url): string
    {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new ContentFetchException("Invalid URL: {$url}");
        }

        // Use WordPress HTTP API
        $response = wp_safe_remote_get($url, [
            'timeout' => 30,
            'user-agent' => 'WP-CLI-Make-Command/1.0',
        ]);

        if (is_wp_error($response)) {
            throw new ContentFetchException(
                sprintf('HTTP error: %s', $response->get_error_message())
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code !== 200) {
            throw new ContentFetchException(
                sprintf('HTTP %d error from %s', $status_code, $url)
            );
        }

        $content = wp_remote_retrieve_body($response);

        // Sanitize HTML content
        if (self::isHtml($content)) {
            $content = wp_kses_post($content);
        }

        return $content;
    }

    /**
     * Read content from STDIN
     *
     * @throws ContentFetchException
     */
    private static function resolveFromStdin(): string
    {
        // Check if STDIN is readable
        if (!defined('STDIN') || !stream_isatty(STDIN)) {
            $content = stream_get_contents(STDIN);

            if ($content === false) {
                throw new ContentFetchException("Failed to read from STDIN");
            }

            return $content;
        }

        throw new ContentFetchException("No input provided via STDIN");
    }

    /**
     * Generate content automatically
     */
    private static function generateContent(string $title): string
    {
        // Simple placeholder generator
        // Bisa di-extend dengan AI, templates, dll

        $templates = [
            "Artikel tentang {$title} membahas berbagai aspek penting...",
            "Dalam tulisan ini, kita akan membahas {$title} secara mendetail...",
            "{$title} merupakan topik yang menarik untuk dikupas...",
        ];

        $template = $templates[array_rand($templates)];

        return $template . "\n\n" . self::generateLoremIpsum();
    }

    /**
     * Generate placeholder text
     */
    private static function generateLoremIpsum(int $paragraphs = 3): string
    {
        $lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. ";
        $lorem .= "Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ";
        $lorem .= "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.";

        return str_repeat($lorem . "\n\n", $paragraphs);
    }

    /**
     * Check if content is HTML
     */
    private static function isHtml(string $content): bool
    {
        return $content !== strip_tags($content);
    }

    /**
     * Detect content source type
     */
    public static function detectSourceType(array $args): string
    {
        if (isset($args['content']) && $args['content'] === '-') {
            return 'stdin';
        }

        if (isset($args['content'])) {
            return 'direct';
        }

        if (isset($args['content-file'])) {
            return 'file';
        }

        if (isset($args['content-url'])) {
            return 'url';
        }

        if (isset($args['generate-content'])) {
            return 'generated';
        }

        return 'none';
    }
}
