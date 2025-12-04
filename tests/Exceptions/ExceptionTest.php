<?php

namespace Vigihdev\WpCliMake\Tests\Exceptions;

use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\Exceptions\ContentFetchException;
use Vigihdev\WpCliMake\Tests\TestCase;
use Vigihdev\WpCliMake\Utils\ContentResolver;

final class ExceptionTest extends TestCase
{

    #[Test]
    public function it_throws_content_fetch_exception(): void
    {
        $this->expectException(ContentFetchException::class);
        $this->expectExceptionCode(ContentFetchException::CODE_FILE_NOT_FOUND);

        ContentResolver::resolveFromFile('/nonexistent/file.txt');
    }
}
