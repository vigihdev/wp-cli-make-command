<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Utils;


final class HttpClient
{
    /**
     * Safe HTTP GET request dengan retry logic
     */
    public static function safeGet(string $url, array $args = []): array
    {
        $defaults = [
            'timeout' => 30,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent' => 'WP-CLI-Make-Command/1.0',
            'blocking' => true,
            'headers' => [],
            'cookies' => [],
            'body' => null,
            'compress' => false,
            'decompress' => true,
            'sslverify' => true,
            'stream' => false,
            'filename' => null,
        ];

        $args = wp_parse_args($args, $defaults);

        // Add retry logic
        $max_retries = 3;
        $retry_count = 0;

        while ($retry_count < $max_retries) {
            $response = wp_remote_get($url, $args);

            if (!is_wp_error($response)) {
                return [
                    'success' => true,
                    'response' => $response,
                    'attempts' => $retry_count + 1,
                ];
            }

            $retry_count++;

            if ($retry_count < $max_retries) {
                sleep(1); // Wait before retry
            }
        }

        return [
            'success' => false,
            'error' => $response,
            'attempts' => $retry_count,
        ];
    }

    /**
     * Extract text from HTML response
     */
    public static function extractTextFromHtml(string $html): string
    {
        // Remove scripts, styles, comments
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<!--(.*?)-->/', '', $html);

        // Convert HTML to plain text
        $text = strip_tags($html);

        // Clean up whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        return $text;
    }
}
