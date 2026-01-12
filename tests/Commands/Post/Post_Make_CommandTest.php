<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Tests\Commands\Post;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\Tests\TestCase;
use Vigihdev\WpCliModels\DTOs\Args\Post\CreatePostArgsDto;

final class Post_Make_CommandTest extends TestCase
{

    private PostMockCommand $command;

    private string $post_title = 'Post Make Command Test';

    private $post_content_valid = 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.\n Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->command = new PostMockCommand();
        parent::setUp();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    #[Test]
    public function it_should_pass_must_be_transform_assoc_argument_to_dto(): void
    {
        $command = $this->command;
        $assoc_args = [
            'post_content' => $this->post_content_valid,
            'post_date' => '2020-09-09 09:09:09',
        ];
        $command->__invoke([$this->post_title], $assoc_args);
        $dto = $command->transformAassocArgumentToDtoPublic($assoc_args);
        Assert::assertInstanceOf(CreatePostArgsDto::class, $dto);
        Assert::assertNull($dto->getGuid());
        $this->assertEquals($dto->getTitle(), $this->post_title);
        $this->assertEquals($dto->getDate(), '2020-09-09 09:09:09');
        $this->assertEquals(substr($dto->getContent(), 0, 5), 'Lorem');
    }
}
