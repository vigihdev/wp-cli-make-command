<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\{DateException, PostFactoryException};
use Vigihdev\WpCliModels\DTOs\Entities\Post\PostEntityDto;
use Vigihdev\WpCliModels\Entities\{PostEntity, UserEntity};
use Vigihdev\WpCliModels\Enums\{PostStatus, PostType};

final class PostFactoryValidator
{
    private ?PostEntityDto $post = null;

    public function __construct(
        private readonly array $data
    ) {
        $this->post = PostEntityDto::fromArray(array_merge($data, ['ID' => 0]));
    }

    public static function validate(array $data): self
    {
        return new self($data);
    }

    public function validateCreate(): self
    {
        return $this
            ->mustBeValidTitle()
            ->mustBeUniqueTitle()
            ->mustBeValidAuthor()
            ->mustBeValidName()
            ->mustHaveContent()
            ->mustHaveValidType()
            ->mustBeValidDate()
            ->mustBeValidDateIfDefined()
            ->mustBeValidStatus();
    }

    public function mustBeValidTitle(): self
    {
        $title = $this->post->getTitle();
        $field = 'post_title';

        StringValidator::validate($title, $field)
            ->notEmpty()
            ->minLength(10)
            ->notMatches('/[^a-z-A-Z-0-9\s\-]+/m');

        return $this;
    }

    public function mustBeValidAuthor(): self
    {
        $author = (int)$this->post->getAuthor();
        $field = 'post_author';

        if ($author <= 0) {
            throw PostFactoryException::invalidAuthorFormat();
        }

        $exist = UserEntity::get((int)$author);
        if ($exist === null) {
            throw PostFactoryException::authorNotFound((int)$author);
        }

        return $this;
    }

    public function mustBeValidDate(): self
    {
        $date = $this->post->getDate();
        $field = 'post_date';

        DateValidator::validate($field, $date)
            ->notEmpty()
            ->dateTimeFormat();

        return $this;
    }

    public function mustBeUniqueTitle(): self
    {
        $this->mustBeValidTitle();
        $title = $this->post->getTitle();
        $field = 'post_title';

        if (PostEntity::existsByTitle($title)) {
            $post = PostEntity::findByTitle($title);
            if (!in_array($post->post_type, [
                PostType::ATTACHMENT->value,
                PostType::NAV_MENU_ITEM->value
            ])) {
                throw PostFactoryException::duplicatePostTitle($title, $post->post_type);
            }
            return $this;
        }

        return $this;
    }

    public function mustHaveContent(): self
    {
        $content = $this->post->getContent();
        $field = 'post_content';

        StringValidator::validate($content, $field)
            ->notEmpty()
            ->minLength(500);

        return $this;
    }

    public function mustBeValidStatus(): self
    {
        $status = $this->post->getStatus();
        $field = 'post_status';
        $validStatuses = [
            PostStatus::DRAFT->value,
            PostStatus::PUBLISH->value,
            PostStatus::PENDING->value,
            PostStatus::PRIVATE->value,
            PostStatus::TRASH->value,
            PostStatus::AUTO_DRAFT->value,
            PostStatus::INHERIT->value,
        ];

        if ($status === null || trim($status) === '') {
            throw PostFactoryException::missingStatus();
        }

        if ($status !== null && !in_array($status, $validStatuses, true)) {
            throw PostFactoryException::invalidStatus();
        }
        return $this;
    }

    public function mustHaveValidType(): self
    {
        $type = $this->post->getType();
        $field = 'post_type';

        if ($type === null || trim($type) === '') {
            throw PostFactoryException::missingType();
        }
        return $this;
    }

    public function mustTypeEqual(string $type): self
    {
        $this->mustHaveValidType();
        $postType = $this->post->getType();
        $field = 'post_type';
        if ($postType !== $type) {
            throw PostFactoryException::invalidType($postType, $type);
        }
        return $this;
    }

    public function mustBeValidName(): self
    {
        $name = $this->post->getName();
        $field = 'post_name';

        if ($name === null || trim($name) === '') {
            throw PostFactoryException::missingName();
        }

        if (PostEntity::existsByName($name)) {
            throw PostFactoryException::duplicatePostName($name);
        }
        return $this;
    }

    public function mustBeValidDateIfDefined(): self
    {
        $dateFields = [
            'post_modified' => $this->post->getModified(),
            'post_date_gmt' => $this->post->getDateGmt(),
            'post_modified_gmt' => $this->post->getModifiedGmt(),
        ];

        foreach ($dateFields as $key => $value) {
            if ($value !== null && $value !== '') {
                DateValidator::validate($key, $value)
                    ->notEmpty()
                    ->dateTimeFormat();
            }
        }

        return $this;
    }
}
