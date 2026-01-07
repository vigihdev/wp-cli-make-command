<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Validators;

use Vigihdev\WpCliMake\Exceptions\PostFactoryException;
use Vigihdev\WpCliModels\DTOs\Entities\Post\PostEntityDto;

final class PostFactoryValidator
{

    private ?PostEntityDto $post = null;

    public function __construct(
        private readonly array $data
    ) {
        $this->post = PostEntityDto::fromArray($data);
    }


    public static function validate(array $data): self
    {
        return new self($data);
    }

    public function validateAll(): self
    {
        return $this
            ->mustBeValidId()
            ->mustBeValidAuthor()
            ->mustHaveValidDateFormat()
            ->mustHaveTitle()
            ->mustHaveContent()
            ->mustHaveStatus()
            ->mustBeValidStatus()
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
        $author = $this->post->getAuthor();
        if ($author !== null && !is_numeric($author)) {
            throw PostFactoryException::invalidAuthorFormat();
        }
        return $this;
    }

    public function mustHaveValidDateFormat(): self
    {
        $date = $this->post->getDate();
        if ($date !== null && $date !== '') {
            $d = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
            if (!$d || $d->format('Y-m-d H:i:s') !== $date) {
                throw PostFactoryException::invalidDateFormat();
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
        return $this;
    }

    public function mustBeUniqueTitle(): self
    {
        // This would require database check to ensure uniqueness
        // Implementation would depend on specific requirements
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
        $validStatuses = ['draft', 'publish', 'pending', 'private', 'trash', 'auto-draft', 'inherit'];

        if ($status !== null && !in_array($status, $validStatuses, true)) {
            throw PostFactoryException::invalidStatus();
        }
        return $this;
    }

    public function mustHaveName(): self
    {
        // Add validation for post name if required
        return $this;
    }

    public function mustBeUniqueName(): self
    {
        // This would require database check to ensure uniqueness
        // Implementation would depend on specific requirements
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

    private function mustHaveValidType(): self
    {
        // Add validation for post type if required
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
