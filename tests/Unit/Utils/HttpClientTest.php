<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Tests\Unit\Utils;

use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\Tests\TestCase;
use Vigihdev\WpCliMake\Utils\HttpClient;

final class HttpClientTest extends TestCase
{
    #[Test]
    public function it_extracts_text_from_simple_html(): void
    {
        $html = '<p>Hello World</p>';
        $result = HttpClient::extractTextFromHtml($html);

        $this->assertSame('Hello World', $result);
    }

    #[Test]
    public function it_removes_script_tags(): void
    {
        $html = '<p>Content</p><script>alert("test");</script>';
        $result = HttpClient::extractTextFromHtml($html);

        $this->assertSame('Content', $result);
        $this->assertStringNotContainsString('alert', $result);
    }

    #[Test]
    public function it_removes_style_tags(): void
    {
        $html = '<p>Content</p><style>body { color: red; }</style>';
        $result = HttpClient::extractTextFromHtml($html);

        $this->assertSame('Content', $result);
        $this->assertStringNotContainsString('color', $result);
    }

    #[Test]
    public function it_removes_html_comments(): void
    {
        $html = '<p>Content</p><!-- This is a comment -->';
        $result = HttpClient::extractTextFromHtml($html);

        $this->assertSame('Content', $result);
        $this->assertStringNotContainsString('comment', $result);
    }

    #[Test]
    public function it_cleans_up_whitespace(): void
    {
        $html = '<p>Hello    World</p>  <p>Test</p>';
        $result = HttpClient::extractTextFromHtml($html);

        $this->assertSame('Hello World Test', $result);
    }

    #[Test]
    public function it_handles_empty_html(): void
    {
        $result = HttpClient::extractTextFromHtml('');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_handles_nested_tags(): void
    {
        $html = '<div><p><strong>Bold</strong> text</p></div>';
        $result = HttpClient::extractTextFromHtml($html);

        $this->assertSame('Bold text', $result);
    }

    #[Test]
    public function it_removes_multiple_scripts_and_styles(): void
    {
        $html = '<script>code1</script><p>Content</p><script>code2</script><style>css</style>';
        $result = HttpClient::extractTextFromHtml($html);

        $this->assertSame('Content', $result);
    }
}
