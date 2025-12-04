<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Tests\Unit\Utils;

use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\Exceptions\ContentFetchException;
use Vigihdev\WpCliMake\Tests\TestCase;
use Vigihdev\WpCliMake\Utils\ContentResolver;

final class ContentResolverTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'test_content');
        if ($this->tempFile === false) {
            $this->fail('Failed to create temporary file.');
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        parent::tearDown();
    }

    #[Test]
    public function it_resolves_direct_content(): void
    {
        $args = ['content' => 'Test content'];
        $result = ContentResolver::resolve($args);

        $this->assertSame('Test content', $result);
    }

    #[Test]
    public function it_resolves_from_file(): void
    {
        file_put_contents($this->tempFile, 'File content');
        $args = ['content-file' => $this->tempFile];
        $result = ContentResolver::resolve($args);

        $this->assertSame('File content', $result);
    }

    #[Test]
    public function it_throws_exception_when_file_not_found(): void
    {
        $this->expectException(ContentFetchException::class);
        $this->expectExceptionMessage('File not found');

        $args = ['content-file' => '/nonexistent/file.txt'];
        ContentResolver::resolve($args);
    }

    #[Test]
    public function it_returns_default_content_when_no_source(): void
    {
        $result = ContentResolver::resolve([], 'Default content');

        $this->assertSame('Default content', $result);
    }

    #[Test]
    public function it_generates_content_when_requested(): void
    {
        $args = ['generate-content' => true, 'title' => 'Test Title'];
        $result = ContentResolver::resolve($args);

        $this->assertStringContainsString('Test Title', $result);
        $this->assertStringContainsString('Lorem ipsum', $result);
    }

    #[Test]
    public function it_detects_direct_source_type(): void
    {
        $args = ['content' => 'Test'];
        $type = ContentResolver::detectSourceType($args);

        $this->assertSame('direct', $type);
    }

    #[Test]
    public function it_detects_stdin_source_type(): void
    {
        $args = ['content' => '-'];
        $type = ContentResolver::detectSourceType($args);

        $this->assertSame('stdin', $type);
    }

    #[Test]
    public function it_detects_file_source_type(): void
    {
        $args = ['content-file' => 'file.txt'];
        $type = ContentResolver::detectSourceType($args);

        $this->assertSame('file', $type);
    }

    #[Test]
    public function it_detects_url_source_type(): void
    {
        $args = ['content-url' => 'https://example.com'];
        $type = ContentResolver::detectSourceType($args);

        $this->assertSame('url', $type);
    }

    #[Test]
    public function it_detects_generated_source_type(): void
    {
        $args = ['generate-content' => true];
        $type = ContentResolver::detectSourceType($args);

        $this->assertSame('generated', $type);
    }

    #[Test]
    public function it_detects_none_source_type(): void
    {
        $type = ContentResolver::detectSourceType([]);

        $this->assertSame('none', $type);
    }

    #[Test]
    public function it_prioritizes_direct_content_over_file(): void
    {
        file_put_contents($this->tempFile, 'File content');
        $args = [
            'content' => 'Direct content',
            'content-file' => $this->tempFile,
        ];
        $result = ContentResolver::resolve($args);

        $this->assertSame('Direct content', $result);
    }

    #[Test]
    public function it_prioritizes_file_over_generated(): void
    {
        file_put_contents($this->tempFile, 'File content');
        $args = [
            'content-file' => $this->tempFile,
            'generate-content' => true,
        ];
        $result = ContentResolver::resolve($args);

        $this->assertSame('File content', $result);
    }
}
