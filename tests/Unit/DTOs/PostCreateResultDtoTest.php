<?php

namespace Vigihdev\WpCliMake\Tests\Unit\DTOs;

use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\DTOs\PostCreateResultDto;
use Vigihdev\WpCliMake\DTOs\PostDataResultDto;
use Vigihdev\WpCliMake\Tests\TestCase;

final class PostCreateResultDtoTest extends TestCase
{
    #[Test]
    public function it_creates_success_result(): void
    {
        $post = get_post(41);
        $result = PostCreateResultDto::success($post);

        $this->assertTrue($result->isCreated());
        $this->assertSame($post, $result->getPost());
        $this->assertNull($result->getError());
        $this->assertFalse($result->isDuplicate());
    }

    #[Test]
    public function it_creates_error_result(): void
    {
        $result = PostCreateResultDto::error('Something went wrong');

        $this->assertFalse($result->isCreated());
        $this->assertNull($result->getPost());
        $this->assertEquals('Something went wrong', $result->getError());
    }

    #[Test]
    public function it_creates_duplicate_result(): void
    {
        $result = PostCreateResultDto::duplicate();

        $this->assertFalse($result->isCreated());
        $this->assertTrue($result->isDuplicate());
        $this->assertStringContainsString('Duplicate', $result->getError());
    }

    #[Test]
    public function it_handles_null_post(): void
    {
        $result = new PostCreateResultDto(true, null);

        $this->assertTrue($result->isCreated());
        $this->assertNull($result->getPost());
    }
}
