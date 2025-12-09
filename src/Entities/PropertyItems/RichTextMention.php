<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\PropertyItems;

use Jensvandewiel\LaravelNotionApi\Entities\Entity;

/**
 * Class RichTextMention.
 *
 * Represents a mention within a rich text object.
 */
class RichTextMention extends Entity
{
    /**
     * @var string
     */
    protected string $mentionType;

    /**
     * @var array|null
     */
    protected ?array $databaseData = null;

    /**
     * @var array|null
     */
    protected ?array $dateData = null;

    /**
     * @var array|null
     */
    protected ?array $linkPreviewData = null;

    /**
     * @var array|null
     */
    protected ?array $pageData = null;

    /**
     * @var array|null
     */
    protected ?array $templateMentionData = null;

    /**
     * @var array|null
     */
    protected ?array $userData = null;

    /**
     * @param array $responseData
     */
    protected function setResponseData(array $responseData): void
    {
        $this->responseData = $responseData;
        $this->fillFromRaw();
    }

    protected function fillFromRaw(): void
    {
        $this->mentionType = $this->responseData['type'] ?? '';

        switch ($this->mentionType) {
            case 'database':
                $this->databaseData = $this->responseData['database'] ?? null;
                break;
            case 'date':
                $this->dateData = $this->responseData['date'] ?? null;
                break;
            case 'link_preview':
                $this->linkPreviewData = $this->responseData['link_preview'] ?? null;
                break;
            case 'page':
                $this->pageData = $this->responseData['page'] ?? null;
                break;
            case 'template_mention':
                $this->templateMentionData = $this->responseData['template_mention'] ?? null;
                break;
            case 'user':
                $this->userData = $this->responseData['user'] ?? null;
                break;
        }
    }

    /**
     * Get the type of mention (database, date, link_preview, page, template_mention, user).
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->mentionType;
    }

    /**
     * Check if this is a database mention.
     *
     * @return bool
     */
    public function isDatabase(): bool
    {
        return $this->mentionType === 'database';
    }

    /**
     * Check if this is a date mention.
     *
     * @return bool
     */
    public function isDate(): bool
    {
        return $this->mentionType === 'date';
    }

    /**
     * Check if this is a link preview mention.
     *
     * @return bool
     */
    public function isLinkPreview(): bool
    {
        return $this->mentionType === 'link_preview';
    }

    /**
     * Check if this is a page mention.
     *
     * @return bool
     */
    public function isPage(): bool
    {
        return $this->mentionType === 'page';
    }

    /**
     * Check if this is a template mention.
     *
     * @return bool
     */
    public function isTemplateMention(): bool
    {
        return $this->mentionType === 'template_mention';
    }

    /**
     * Check if this is a user mention.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->mentionType === 'user';
    }

    /**
     * Get database mention data.
     *
     * @return array|null
     */
    public function getDatabaseData(): ?array
    {
        return $this->databaseData;
    }

    /**
     * Get database ID if this is a database mention.
     *
     * @return string|null
     */
    public function getDatabaseId(): ?string
    {
        return $this->databaseData['id'] ?? null;
    }

    /**
     * Get date mention data.
     *
     * @return array|null
     */
    public function getDateData(): ?array
    {
        return $this->dateData;
    }

    /**
     * Get date start value.
     *
     * @return string|null
     */
    public function getDateStart(): ?string
    {
        return $this->dateData['start'] ?? null;
    }

    /**
     * Get date end value.
     *
     * @return string|null
     */
    public function getDateEnd(): ?string
    {
        return $this->dateData['end'] ?? null;
    }

    /**
     * Get link preview mention data.
     *
     * @return array|null
     */
    public function getLinkPreviewData(): ?array
    {
        return $this->linkPreviewData;
    }

    /**
     * Get link preview URL.
     *
     * @return string|null
     */
    public function getLinkPreviewUrl(): ?string
    {
        return $this->linkPreviewData['url'] ?? null;
    }

    /**
     * Get page mention data.
     *
     * @return array|null
     */
    public function getPageData(): ?array
    {
        return $this->pageData;
    }

    /**
     * Get page ID if this is a page mention.
     *
     * @return string|null
     */
    public function getPageId(): ?string
    {
        return $this->pageData['id'] ?? null;
    }

    /**
     * Get template mention data.
     *
     * @return array|null
     */
    public function getTemplateMentionData(): ?array
    {
        return $this->templateMentionData;
    }

    /**
     * Get template mention type (template_mention_date or template_mention_user).
     *
     * @return string|null
     */
    public function getTemplateMentionType(): ?string
    {
        return $this->templateMentionData['type'] ?? null;
    }

    /**
     * Get template mention date value (today or now).
     *
     * @return string|null
     */
    public function getTemplateMentionDate(): ?string
    {
        return $this->templateMentionData['template_mention_date'] ?? null;
    }

    /**
     * Get template mention user value (me).
     *
     * @return string|null
     */
    public function getTemplateMentionUser(): ?string
    {
        return $this->templateMentionData['template_mention_user'] ?? null;
    }

    /**
     * Get user mention data.
     *
     * @return array|null
     */
    public function getUserData(): ?array
    {
        return $this->userData;
    }

    /**
     * Get user ID if this is a user mention.
     *
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userData['id'] ?? null;
    }

    /**
     * Get user object type.
     *
     * @return string|null
     */
    public function getUserObjectType(): ?string
    {
        return $this->userData['object'] ?? null;
    }
}

