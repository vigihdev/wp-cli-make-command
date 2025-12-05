<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Tests\Unit\Utils;

use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Vigihdev\WpCliMake\Exceptions\ContentFetchException;
use Vigihdev\WpCliMake\Tests\TestCase;
use Vigihdev\WpCliMake\Utils\FilepathTransformerDto;

final class FilepathTransformerDtoTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/wp-cli-test-' . uniqid();
        mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            array_map('unlink', glob($this->tempDir . '/*'));
            rmdir($this->tempDir);
        }
        parent::tearDown();
    }

    #[Test]
    public function it_throws_exception_when_json_file_not_found(): void
    {
        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('File not found');

        FilepathTransformerDto::fromFileJson('/nonexistent/file.json', \stdClass::class);
    }

    #[Test]
    public function it_throws_exception_when_json_file_not_readable(): void
    {
        $filepath = $this->tempDir . '/unreadable.json';
        file_put_contents($filepath, '{"test": "data"}');
        chmod($filepath, 0000);

        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('not readable');

        try {
            FilepathTransformerDto::fromFileJson($filepath, \stdClass::class);
        } finally {
            chmod($filepath, 0644);
        }
    }

    #[Test]
    public function it_throws_exception_when_json_file_is_empty(): void
    {
        $filepath = $this->tempDir . '/empty.json';
        file_put_contents($filepath, '   ');

        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('empty');

        FilepathTransformerDto::fromFileJson($filepath, \stdClass::class);
    }

    #[Test]
    public function it_throws_exception_when_json_file_has_invalid_json(): void
    {
        $filepath = $this->tempDir . '/invalid.json';
        file_put_contents($filepath, '{invalid json}');

        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('Invalid JSON');

        FilepathTransformerDto::fromFileJson($filepath, \stdClass::class);
    }

    #[Test]
    public function it_throws_exception_when_csv_file_not_found(): void
    {
        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('File not found');

        FilepathTransformerDto::fromFileCsv('/nonexistent/file.csv', \stdClass::class);
    }

    #[Test]
    public function it_throws_exception_when_csv_file_not_readable(): void
    {
        $filepath = $this->tempDir . '/unreadable.csv';
        file_put_contents($filepath, 'col1,col2');
        chmod($filepath, 0000);

        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('not readable');

        try {
            FilepathTransformerDto::fromFileCsv($filepath, \stdClass::class);
        } finally {
            chmod($filepath, 0644);
        }
    }

    #[Test]
    public function it_throws_exception_when_csv_file_is_empty(): void
    {
        $filepath = $this->tempDir . '/empty.csv';
        file_put_contents($filepath, '');

        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('empty');

        FilepathTransformerDto::fromFileCsv($filepath, \stdClass::class);
    }

    #[Test]
    public function it_throws_exception_when_csv_has_empty_lines_only(): void
    {
        $filepath = $this->tempDir . '/empty-lines.csv';
        file_put_contents($filepath, "\n\n\n");

        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('empty');

        FilepathTransformerDto::fromFileCsv($filepath, \stdClass::class);
    }

    #[Test]
    public function it_throws_runtime_exception_when_dto_transform_fails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gagal transform');

        FilepathTransformerDto::fromJson('{"test": "data"}', 'NonExistentClass');
    }

    #[Test]
    public function it_throws_runtime_exception_when_dto_transform_fails_from_file(): void
    {
        $filepath = $this->tempDir . '/valid.json';
        file_put_contents($filepath, '{"test": "data"}');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gagal transform');

        FilepathTransformerDto::fromFileJson($filepath, 'NonExistentClass');
    }

    #[Test]
    public function it_throws_runtime_exception_when_csv_transform_fails(): void
    {
        $filepath = $this->tempDir . '/valid.csv';
        file_put_contents($filepath, "col1,col2\nval1,val2");

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gagal transform');

        FilepathTransformerDto::fromFileCsv($filepath, 'NonExistentClass');
    }
}
