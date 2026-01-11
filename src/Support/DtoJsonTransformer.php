<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Support;

use Serializer\Factory\JsonTransformerFactory;
use Vigihdev\Support\Collection;

final class DtoJsonTransformer
{
    public static function fromFile(string $filepath, string $dtoClass): Collection
    {
        try {
            $transformer = JsonTransformerFactory::create($dtoClass);
            $objects = $transformer->transformWithFile($filepath);
            $data = is_array($objects) ? $objects : [$objects];
            return new Collection($data);
        } catch (\Throwable $e) {
            throw new \RuntimeException("Error reading file {$filepath}: {$e->getMessage()}");
        }
    }
}
