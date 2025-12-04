<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Utils;

use Vigihdev\WpCliMake\Exceptions\ContentFetchException;

final class FilepathResolver
{

    public static function fromFileJson(string $filepath)
    {
        if (!is_file($filepath)) {
            throw ContentFetchException::fileNotFound($filepath);
        }

        if (!is_readable($filepath)) {
            throw ContentFetchException::unreadableFile($filepath);
        }

        $json = file_get_contents($filepath);

        if (trim($json) === '') {
            throw ContentFetchException::emptyFile($filepath);
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ContentFetchException::invalidJson($filepath);
        }

        return $data;
    }

    /**
     * Load and parse CSV file.
     * CSV supports:
     *   name
     *   name,slug
     *
     * @throws ContentFetchException
     */
    public static function fromFileCsv(string $filepath): array
    {
        if (!is_file($filepath)) {
            throw ContentFetchException::fileNotFound($filepath);
        }

        if (!is_readable($filepath)) {
            throw ContentFetchException::unreadableFile($filepath);
        }

        // cepat, aman, handle error lewat exception manual
        $rows = @file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($rows === false) {
            throw ContentFetchException::invalidCsv($filepath);
        }

        // CSV kosong
        if (count($rows) === 0) {
            throw ContentFetchException::emptyFile($filepath);
        }

        $data = [];
        foreach ($rows as $index => $line) {
            $cols = str_getcsv($line);

            // minimal 1 kolom
            if (empty($cols)) {
                throw ContentFetchException::invalidCsv($filepath);
            }

            $data[] = $cols;
        }

        return $data;
    }
}
