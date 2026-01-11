<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\PostFactoryException;
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
            ->mustBeValidAuthor()
            ->mustHaveValidDateFormat()
            ->mustHaveTitle()
            ->mustBeUniqueTitle()
            ->mustHaveValidType()
            ->mustHaveContent()
            ->mustHaveStatus()
            ->mustBeValidStatus()
            ->mustHaveName()
            ->mustBeUniqueName()
            ->hasValidDateGmtFormat();
    }

    public function getData(): PostEntityDto
    {
        return $this->post;
    }

    public function mustBeValidId(): self
    {
        $id = $this->post->getId();
        if ($id !== null && (!is_numeric($id) || $id <= 0)) {
            throw PostFactoryException::invalidIdValue((int)$id);
        }
        return $this;
    }

    public function mustBeValidAuthor(): self
    {
        $author = (int)$this->post->getAuthor();
        if ($author <= 0) {
            throw PostFactoryException::invalidAuthorFormat();
        }
        $exist = UserEntity::get((int)$author);
        if ($exist === null) {
            throw PostFactoryException::authorNotFound((int)$author);
        }
        return $this;
    }

    public function mustHaveValidDateFormat(): self
    {
        $date = $this->post->getDate();
        if ($date !== null && $date !== '') {
            $d = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
            if (!$d || $d->format('Y-m-d H:i:s') !== $date) {
                throw PostFactoryException::invalidDateFormat('post_date', $date);
            }
        }
        return $this;
    }

    public function mustHaveTitle(): self
    {
        $title = $this->post->getTitle();
        if ($title === null || trim($title) === '') {
            throw PostFactoryException::missingTitle();
        }

        if (strlen($title) < 10) {
            throw StringValidator::validate($title, 'post_title')->minLength(10);
        }

        if (preg_match('/[^a-z-A-Z-0-9\s\-]+/', $title)) {
            throw PostFactoryException::invalidCharacters('post_title', $title);
        }

        return $this;
    }

    public function mustBeUniqueTitle(): self
    {
        $this->mustHaveTitle();
        $title = $this->post->getTitle();

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
        if ($content === null || trim($content) === '') {
            throw PostFactoryException::missingContent();
        }
        return $this;
    }

    public function mustHaveStatus(): self
    {
        $status = $this->post->getStatus();
        if ($status === null || trim($status) === '') {
            throw PostFactoryException::missingStatus();
        }
        return $this;
    }

    public function mustBeValidStatus(): self
    {
        $status = $this->post->getStatus();
        $validStatuses = [
            PostStatus::DRAFT->value,
            PostStatus::PUBLISH->value,
            PostStatus::PENDING->value,
            PostStatus::PRIVATE->value,
            PostStatus::TRASH->value,
            PostStatus::AUTO_DRAFT->value,
            PostStatus::INHERIT->value,
        ];

        if ($status !== null && !in_array($status, $validStatuses, true)) {
            throw PostFactoryException::invalidStatus();
        }
        return $this;
    }

    public function mustHaveValidType(): self
    {
        $type = $this->post->getType();
        if ($type === null || trim($type) === '') {
            throw PostFactoryException::missingType();
        }
        return $this;
    }

    public function mustTypeEqual(string $type): self
    {
        $this->mustHaveValidType();
        $postType = $this->post->getType();
        if ($postType !== $type) {
            throw PostFactoryException::invalidType($postType, $type);
        }
        return $this;
    }

    public function mustHaveName(): self
    {
        $name = $this->post->getName();
        if ($name === null || trim($name) === '') {
            throw PostFactoryException::missingName();
        }
        return $this;
    }

    public function mustBeUniqueName(): self
    {
        $this->mustHaveName();
        $name = $this->post->getName();
        if (PostEntity::existsByName($name)) {
            throw PostFactoryException::duplicatePostName($name);
        }
        return $this;
    }

    public function hasValidDateGmtFormat(): self
    {
        $dateGmt = $this->post->getDateGmt();
        if ($dateGmt !== null && $dateGmt !== '') {
            $d = \DateTime::createFromFormat('Y-m-d H:i:s', $dateGmt);
            if (!$d || $d->format('Y-m-d H:i:s') !== $dateGmt) {
                throw PostFactoryException::invalidDateGmtFormat();
            }
        }
        return $this;
    }

    private function mustHaveExcerpt(): self
    {
        // Add validation for excerpt if required
        return $this;
    }

    private function mustHaveValidCommentStatus(): self
    {
        $commentStatus = $this->post->getCommentStatus();
        if ($commentStatus !== null && !in_array($commentStatus, ['open', 'closed'], true)) {
            // Add appropriate exception if needed
        }
        return $this;
    }

    private function mustHaveValidPingStatus(): self
    {
        $pingStatus = $this->post->getPingStatus();
        if ($pingStatus !== null && !in_array($pingStatus, ['open', 'closed'], true)) {
            // Add appropriate exception if needed
        }
        return $this;
    }

    private function mustHavePassword(): self
    {
        // Add validation for password if required
        return $this;
    }

    private function mustHaveToPing(): self
    {
        // Add validation for to_ping if required
        return $this;
    }

    private function mustHavePinged(): self
    {
        // Add validation for pinged if required
        return $this;
    }

    private function mustHaveValidModifiedDate(): self
    {
        $modified = $this->post->getModified();
        if ($modified !== null && $modified !== '') {
            $d = \DateTime::createFromFormat('Y-m-d H:i:s', $modified);
            if (!$d || $d->format('Y-m-d H:i:s') !== $modified) {
                // Add appropriate exception if needed
            }
        }
        return $this;
    }

    private function mustHaveValidModifiedGmt(): self
    {
        $modifiedGmt = $this->post->getModifiedGmt();
        if ($modifiedGmt !== null && $modifiedGmt !== '') {
            $d = \DateTime::createFromFormat('Y-m-d H:i:s', $modifiedGmt);
            if (!$d || $d->format('Y-m-d H:i:s') !== $modifiedGmt) {
                // Add appropriate exception if needed
            }
        }
        return $this;
    }

    private function mustHaveContentFiltered(): self
    {
        // Add validation for content_filtered if required
        return $this;
    }

    private function mustHaveValidParent(): self
    {
        $parent = $this->post->getParent();
        if ($parent !== null && (!is_numeric($parent) || $parent < 0)) {
            // Add appropriate exception if needed
        }
        return $this;
    }

    private function mustHaveGuid(): self
    {
        // Add validation for guid if required
        return $this;
    }

    private function mustHaveValidMenuOrder(): self
    {
        $menuOrder = $this->post->getMenuOrder();
        if ($menuOrder !== null && !is_numeric($menuOrder)) {
            // Add appropriate exception if needed
        }
        return $this;
    }


    private function mustHaveMimeType(): self
    {
        // Add validation for mime type if required
        return $this;
    }

    private function mustHaveValidCommentCount(): self
    {
        $commentCount = $this->post->getCommentCount();
        if ($commentCount !== null && (!is_numeric($commentCount) || $commentCount < 0)) {
            // Add appropriate exception if needed
        }
        return $this;
    }

    private function mustHaveFilter(): self
    {
        // Add validation for filter if required
        return $this;
    }
}
