<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Tests\Unit\Factory;

use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\Factory\PostFactory;
use Vigihdev\WpCliMake\Tests\TestCase;

final class PostFactoryTest extends TestCase
{
    #[Test]
    public function it_validates_post_status(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'validateStatus');
        $method->setAccessible(true);

        $this->assertEquals('draft', $method->invoke(null, 'draft'));
        $this->assertEquals('publish', $method->invoke(null, 'publish'));
        $this->assertEquals('pending', $method->invoke(null, 'pending'));
        $this->assertEquals('draft', $method->invoke(null, 'invalid'));
    }

    #[Test]
    public function it_generates_slug_from_title(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'preparePostData');
        $method->setAccessible(true);

        $result = $method->invoke(null, 'Hello World Test', []);

        $this->assertEquals('hello-world-test', $result['post_name']);
    }

    #[Test]
    public function it_validates_post_type(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'validateType');
        $method->setAccessible(true);

        $this->assertEquals('post', $method->invoke(null, 'post'));
        $this->assertEquals('page', $method->invoke(null, 'page'));
        $this->assertEquals('post', $method->invoke(null, 'invalid_type'));
    }

    #[Test]
    public function it_normalizes_title(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'normalizeTitle');
        $method->setAccessible(true);

        $this->assertEquals('Hello World', $method->invoke(null, '  Hello   World  '));
        $this->assertEquals('Test Title', $method->invoke(null, 'Test Title'));
    }

    #[Test]
    public function it_makes_unique_title(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'makeUniqueTitle');
        $method->setAccessible(true);

        $result = $method->invoke(null, 'Test Title');

        $this->assertStringContainsString('Test Title', $result);
        $this->assertStringContainsString('-', $result);
    }

    #[Test]
    public function it_validates_date(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'validateDate');
        $method->setAccessible(true);

        $result = $method->invoke(null, '2024-01-01 10:00:00');
        $this->assertEquals('2024-01-01 10:00:00', $result);

        $result = $method->invoke(null, 'invalid-date');
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $result);
    }

    #[Test]
    public function it_prepares_post_data_with_defaults(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'preparePostData');
        $method->setAccessible(true);

        $result = $method->invoke(null, 'Test Title', []);

        $this->assertEquals('Test Title', $result['post_title']);
        $this->assertEquals('draft', $result['post_status']);
        $this->assertEquals('post', $result['post_type']);
        $this->assertEquals('test-title', $result['post_name']);
    }

    #[Test]
    public function it_prepares_post_data_with_custom_args(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'preparePostData');
        $method->setAccessible(true);

        $args = [
            'content' => 'Test content',
            'status' => 'publish',
            'excerpt' => 'Test excerpt',
            'slug' => 'custom-slug',
        ];

        $result = $method->invoke(null, 'Test Title', $args);

        $this->assertEquals('Test content', $result['post_content']);
        $this->assertEquals('publish', $result['post_status']);
        $this->assertEquals('Test excerpt', $result['post_excerpt']);
        $this->assertEquals('custom-slug', $result['post_name']);
    }

    #[Test]
    public function it_finds_post_by_id(): void
    {
        $post = get_post(41);
        $result = PostFactory::find(41);

        $this->assertNotNull($result);
        $this->assertEquals($post->ID, $result->ID);
    }

    #[Test]
    public function it_returns_null_for_invalid_post_id(): void
    {
        $result = PostFactory::find(999999);

        $this->assertNull($result);
    }
}
