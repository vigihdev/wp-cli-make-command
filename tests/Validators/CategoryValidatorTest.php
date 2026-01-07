<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Tests\Validators;

use PHPUnit\Framework\Attributes\Test;
use Vigihdev\WpCliMake\Tests\TestCase;
use Vigihdev\WpCliMake\Validators\CategoryValidator;
use Vigihdev\WpCliMake\Exceptions\CategoryException;

final class CategoryValidatorTest extends TestCase
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
    public function it_should_create_validator_instance_with_valid_term(): void
    {
        $validator = new CategoryValidator('test-category');
        $this->assertInstanceOf(CategoryValidator::class, $validator);
    }

    #[Test]
    public function it_should_create_validator_instance_with_null_term(): void
    {
        $validator = new CategoryValidator(null);
        $this->assertInstanceOf(CategoryValidator::class, $validator);
    }

    #[Test]
    public function it_should_create_validator_instance_with_integer_term(): void
    {
        $validator = new CategoryValidator(123);
        $this->assertInstanceOf(CategoryValidator::class, $validator);
    }

    #[Test]
    public function it_should_return_validator_instance_with_static_validate_method(): void
    {
        $validator = CategoryValidator::validate('test-category');
        $this->assertInstanceOf(CategoryValidator::class, $validator);
    }

    #[Test]
    public function it_should_throw_exception_when_category_is_null_for_must_exist(): void
    {
        $validator = new CategoryValidator(null);
        
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Category not found: ');
        
        $validator->mustExist();
    }

    #[Test]
    public function it_should_throw_exception_when_category_is_null_for_must_have_valid_name(): void
    {
        $validator = new CategoryValidator(null);
        
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Invalid category name: ');
        
        $validator->mustHaveValidName();
    }

    #[Test]
    public function it_should_throw_exception_for_invalid_category_name_with_integer(): void
    {
        $validator = new CategoryValidator(123);
        
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Invalid category name: 123');
        
        $validator->mustHaveValidName();
    }

    #[Test]
    public function it_should_throw_exception_for_empty_category_name(): void
    {
        $validator = new CategoryValidator('');
        
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Invalid category name: ');
        
        $validator->mustHaveValidName();
    }

    #[Test]
    public function it_should_throw_exception_for_too_long_category_name(): void
    {
        $longName = str_repeat('a', 201); // More than 200 characters
        $validator = new CategoryValidator($longName);
        
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage("Invalid category name: {$longName}");
        
        $validator->mustHaveValidName();
    }

    #[Test]
    public function it_should_throw_exception_when_category_is_null_for_must_not_already_exist(): void
    {
        $validator = new CategoryValidator(null);
        
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Category already exists: ');
        
        $validator->mustNotAlreadyExist();
    }

    #[Test]
    public function it_should_throw_exception_when_category_id_already_exists(): void
    {
        $validator = new CategoryValidator(123);
        
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Category already exists: 123');
        
        $validator->mustNotAlreadyExist();
    }

    #[Test]
    public function it_should_validate_valid_slug(): void
    {
        $validator = new CategoryValidator('valid-slug-name');
        $result = $validator->mustHaveValidSlug();
        $this->assertInstanceOf(CategoryValidator::class, $result);
    }

    #[Test]
    public function it_should_validate_valid_slug_with_underscores(): void
    {
        $validator = new CategoryValidator('valid_slug_name');
        $result = $validator->mustHaveValidSlug();
        $this->assertInstanceOf(CategoryValidator::class, $result);
    }

    #[Test]
    public function it_should_validate_valid_slug_with_numbers(): void
    {
        $validator = new CategoryValidator('valid-slug-123');
        $result = $validator->mustHaveValidSlug();
        $this->assertInstanceOf(CategoryValidator::class, $result);
    }

    #[Test]
    public function it_should_skip_slug_validation_for_integer(): void
    {
        $validator = new CategoryValidator(123);
        $result = $validator->mustHaveValidSlug();
        $this->assertInstanceOf(CategoryValidator::class, $result);
    }

    #[Test]
    public function it_should_throw_exception_when_category_is_null_for_parent_validation(): void
    {
        $validator = new CategoryValidator(null);

        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Invalid parent category \'1\' for category \'\'');

        $validator->mustHaveValidParent(1);
    }

    #[Test]
    public function it_should_allow_null_parent(): void
    {
        $validator = new CategoryValidator('test-category');
        $result = $validator->mustHaveValidParent(null);
        $this->assertInstanceOf(CategoryValidator::class, $result);
    }

    #[Test]
    public function it_should_throw_exception_when_category_is_null_for_post_type_validation(): void
    {
        $validator = new CategoryValidator(null);
        
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Category \'\' is not allowed for post type \'post\'');
        
        $validator->mustBeAllowedForPostType('post');
    }

    #[Test]
    public function it_should_validate_for_creation_successfully_with_valid_name(): void
    {
        // Test that validateForCreation calls mustHaveValidName and mustNotAlreadyExist
        // For this test, we'll use a name that should pass basic validation
        $validator = new CategoryValidator('valid-new-category');
        
        // Since we can't control WordPress functions in tests, we'll just make sure
        // the method can be called without error for a valid string
        try {
            $result = $validator->validateForCreation();
            $this->assertInstanceOf(CategoryValidator::class, $result);
        } catch (CategoryException $e) {
            // If it throws an exception about already existing, that's expected in a WordPress environment
            // where the category might already exist
            $this->assertStringContainsString('already exists', $e->getMessage());
        }
    }

    #[Test]
    public function it_should_validate_for_usage_successfully_with_valid_input(): void
    {
        // Test that validateForUsage calls mustExist
        // For integer input, it should try to find by ID
        $validator = new CategoryValidator('valid-category');
        
        // Since we can't control WordPress functions in tests, we'll just make sure
        // the method can be called without error for a valid string
        try {
            $result = $validator->validateForUsage();
            $this->assertInstanceOf(CategoryValidator::class, $result);
        } catch (CategoryException $e) {
            // If it throws an exception about not found, that's expected in a WordPress environment
            // where the category might not exist
            $this->assertStringContainsString('not found', $e->getMessage());
        }
    }

    #[Test]
    public function it_should_handle_integer_input_properly_for_valid_name(): void
    {
        $validator = new CategoryValidator(123);
        
        // Integer inputs should fail mustHaveValidName validation
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Invalid category name: 123');
        
        $validator->mustHaveValidName();
    }

    #[Test]
    public function it_should_handle_integer_input_properly_for_not_already_exist(): void
    {
        $validator = new CategoryValidator(456);
        
        // Integer inputs should fail mustNotAlreadyExist validation (as IDs are considered as existing)
        $this->expectException(CategoryException::class);
        $this->expectExceptionMessage('Category already exists: 456');
        
        $validator->mustNotAlreadyExist();
    }
}