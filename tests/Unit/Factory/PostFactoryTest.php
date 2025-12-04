<?php

namespace Vigihdev\WpCliMake\Tests\Unit\Factory;

use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\Factory\PostFactory;
use Vigihdev\WpCliMake\Tests\TestCase;

final class PostFactoryTest extends TestCase
{
    #[Test]
    public function it_validates_post_status(): void
    {
        // Test static method validation
        $method = new \ReflectionMethod(PostFactory::class, 'validateStatus');
        $method->setAccessible(true);

        $tests = [
            'draft' => 'draft',
            'publish' => 'publish',
            'invalid' => 'draft', // fallback
        ];

        foreach ($tests as $input => $expected) {
            $result = $method->invoke(null, $input);
            $this->assertEquals($expected, $result);
        }
    }

    #[Test]
    public function it_generates_slug_from_title(): void
    {
        $method = new \ReflectionMethod(PostFactory::class, 'preparePostData');
        $method->setAccessible(true);

        $result = $method->invoke(null, 'Hello World Test', []);

        $this->assertEquals('hello-world-test', $result['post_name']);
    }
}
