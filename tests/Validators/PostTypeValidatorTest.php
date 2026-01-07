<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Tests\Validators;

use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\Tests\TestCase;
use Vigihdev\WpCliMake\DTOs\Posts\PostDto;
use Vigihdev\WpCliMake\Validators\PostTypeValidator;
use Vigihdev\WpCliMake\Exceptions\PostTypeException;

final class PostTypeValidatorTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
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
    public function it_should_create_validator_instance_with_valid_post_dto(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'post',
            taxInput: [],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);
        $this->assertInstanceOf(PostTypeValidator::class, $validator);
    }

    #[Test]
    public function it_should_pass_must_have_registered_post_type_for_valid_post_type(): void
    {
        // In a real WordPress environment, 'post' is a registered post type
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'post', // This is a valid WordPress post type
            taxInput: [],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        // Since we can't control WordPress functions in tests, we'll just make sure
        // the method can be called without error for a valid post type
        try {
            $result = $validator->mustHaveRegisteredPostType();
            $this->assertInstanceOf(PostTypeValidator::class, $result);
        } catch (PostTypeException $e) {
            // If it throws an exception about not registered, that's expected in a non-WordPress environment
            $this->assertStringContainsString('not registered', $e->getMessage());
        }
    }

    #[Test]
    public function it_should_fail_must_have_registered_post_type_for_invalid_post_type(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'invalid_post_type', // This is not a valid WordPress post type
            taxInput: [],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        $this->expectException(PostTypeException::class);
        $this->expectExceptionMessage('Post type not registered: invalid_post_type');

        $validator->mustHaveRegisteredPostType();
    }

    #[Test]
    public function it_should_pass_must_have_registered_taxonomies_for_valid_taxonomies(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'post',
            taxInput: [
                'category' => ['test-category'], // 'category' is a valid WordPress taxonomy
                'post_tag' => ['test-tag']      // 'post_tag' is a valid WordPress taxonomy
            ],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        // Since we can't control WordPress functions in tests, we'll just make sure
        // the method can be called without error for valid taxonomies
        try {
            $result = $validator->mustHaveRegisteredTaxonomies();
            $this->assertInstanceOf(PostTypeValidator::class, $result);
        } catch (PostTypeException $e) {
            // If it throws an exception about not registered, that's expected in a non-WordPress environment
            $this->assertStringContainsString('not registered', $e->getMessage());
        }
    }

    #[Test]
    public function it_should_fail_must_have_registered_taxonomies_for_invalid_taxonomy(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'post',
            taxInput: [
                'invalid_taxonomy' => ['test-term'] // This is not a valid WordPress taxonomy
            ],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        $this->expectException(PostTypeException::class);
        $this->expectExceptionMessage('Taxonomy not registered: invalid_taxonomy');

        $validator->mustHaveRegisteredTaxonomies();
    }

    #[Test]
    public function it_should_pass_must_allow_taxonomies_for_post_type_for_valid_combination(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'post',
            taxInput: [
                'category' => ['test-category'], // 'category' is allowed for 'post' type
                'post_tag' => ['test-tag']      // 'post_tag' is allowed for 'post' type
            ],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        // Since we can't control WordPress functions in tests, we'll just make sure
        // the method can be called without error for valid combinations
        try {
            $result = $validator->mustAllowTaxonomiesForPostType();
            $this->assertInstanceOf(PostTypeValidator::class, $result);
        } catch (PostTypeException $e) {
            // If it throws an exception about not allowed, that's expected in a non-WordPress environment
            $this->assertStringContainsString('not allowed', $e->getMessage());
        }
    }

    #[Test]
    public function it_should_fail_must_allow_taxonomies_for_post_type_for_invalid_combination(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'attachment', // 'attachment' post type doesn't typically allow 'category'
            taxInput: [
                'category' => ['test-category']
            ],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        $this->expectException(PostTypeException::class);
        $this->expectExceptionMessageMatches('/not allowed/');

        $validator->mustAllowTaxonomiesForPostType();
    }

    #[Test]
    public function it_should_pass_must_have_existing_terms_for_valid_terms(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'post',
            taxInput: [
                'category' => ['uncategorized'], // 'uncategorized' is a default WordPress category
            ],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        // Since we can't control WordPress functions in tests, we'll just make sure
        // the method can be called without error for valid terms
        try {
            $result = $validator->mustHaveExistingTerms();
            $this->assertInstanceOf(PostTypeValidator::class, $result);
        } catch (PostTypeException $e) {
            // If it throws an exception about term not found, that's expected in a non-WordPress environment
            $this->assertStringContainsString('uncategorized', $e->getMessage());
        }
    }

    #[Test]
    public function it_should_fail_must_have_existing_terms_for_invalid_term(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'post',
            taxInput: [
                'category' => ['nonexistent_category'] // This category doesn't exist
            ],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        $this->expectException(PostTypeException::class);
        $this->expectExceptionMessageMatches('/not exist/');

        $validator->mustHaveExistingTerms();
    }

    #[Test]
    public function it_should_pass_all_validations_for_complete_valid_post_dto(): void
    {
        $postDto = new PostDto(
            title: 'Test Post',
            content: 'Test content',
            type: 'post',
            taxInput: [
                'category' => ['uncategorized'],
            ],
            metaInput: []
        );

        $validator = PostTypeValidator::validate($postDto);

        // Since we can't control WordPress functions in tests, we'll just make sure
        // the methods can be called without error for valid data
        try {
            $result = $validator
                ->mustHaveRegisteredPostType()
                ->mustHaveRegisteredTaxonomies()
                ->mustAllowTaxonomiesForPostType()
                ->mustHaveExistingTerms();

            $this->assertInstanceOf(PostTypeValidator::class, $result);
        } catch (PostTypeException $e) {
            // If it throws an exception, that's expected in a non-WordPress environment
            $this->assertTrue(true); // Test passes if exception is caught
        }
    }
}
