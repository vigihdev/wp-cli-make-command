<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Utils;

use RuntimeException;
use Serializer\Factory\JsonTransformerFactory;
use Vigihdev\WpCliMake\Exceptions\ContentFetchException;

final class FilepathTransformerDto
{

    public static function fromJson(string $json, string $dtoClass): object|array
    {
        return self::dtoTransform($json, $dtoClass);
    }

    public static function fromFileJson(string $filepath, string $dtoClass): object|array
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

        return self::dtoTransform($json, $dtoClass);
    }

    public static function fromFileCsv(string $filepath, string $dtoClass): array
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

        $json = json_encode($data);

        return self::dtoTransform($json, $dtoClass);
    }

    private static function dtoTransform(string $json, string $dtoClass): object|array
    {
        try {
            $json = trim($json);
            $transformer = JsonTransformerFactory::create($dtoClass);
            if (substr($json, 0, 1) === '[') {
                return $transformer->transformArrayJson($json);
            }
            return $transformer->transformJson($json);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                sprintf("Gagal transform from %s %s", $json, $e->getMessage())
            );
        }
    }
}
