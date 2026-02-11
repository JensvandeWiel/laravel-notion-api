<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\PropertyItems;

use Jensvandewiel\LaravelNotionApi\Entities\Entity;
use Illuminate\Support\Arr;
use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\Annotation;
use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\RichTextMention;

/**
 * Class RichTextItem.
 *
 * Represents a single rich text object from the Notion API.
 * Supports text, mention, and equation types.
 */
class RichTextItem extends Entity
{
    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var string
     */
    protected string $plainText = '';

    /**
     * @var string|null
     */
    protected ?string $href = null;

    /**
     * @var Annotation
     */
    protected Annotation $annotations;

    /**
     * Text type specific data.
     * @var string|null
     */
    protected ?string $textContent = null;

    /**
     * Text type link data.
     * @var array|null
     */
    protected ?array $textLink = null;

    /**
     * Mention type data.
     * @var RichTextMention|null
     */
    protected ?RichTextMention $mention = null;

    /**
     * Equation type data.
     * @var string|null
     */
    protected ?string $equationExpression = null;

    /**
     * Create a text-type rich text item.
     */
    public static function fromText(string $content, array $annotations = [], ?string $link = null): self
    {
        return new self([
            'type' => 'text',
            'text' => [
                'content' => $content,
                'link' => $link === null ? null : ['url' => $link],
            ],
            'annotations' => self::normalizeAnnotations($annotations),
            'plain_text' => $content,
            'href' => $link,
        ]);
    }

    /**
     * Export the raw rich text item in Notion API format.
     */
    public function toRaw(): array
    {
        return $this->getRawResponse();
    }

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
        $this->type = $this->responseData['type'] ?? '';
        $this->plainText = $this->responseData['plain_text'] ?? '';
        $this->href = $this->responseData['href'] ?? null;

        // Parse annotations
        if (Arr::exists($this->responseData, 'annotations')) {
            $this->annotations = new Annotation($this->responseData['annotations']);
        } else {
            // Create default annotations if not present
            $this->annotations = new Annotation([
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ]);
        }

        // Parse type-specific data
        switch ($this->type) {
            case 'text':
                $this->parseTextData();
                break;
            case 'mention':
                $this->parseMentionData();
                break;
            case 'equation':
                $this->parseEquationData();
                break;
        }
    }

    protected function parseTextData(): void
    {
        if (Arr::exists($this->responseData, 'text')) {
            $textData = $this->responseData['text'];
            $this->textContent = $textData['content'] ?? '';
            $this->textLink = $textData['link'] ?? null;
        }
    }

    protected function parseMentionData(): void
    {
        if (Arr::exists($this->responseData, 'mention')) {
            $this->mention = new RichTextMention($this->responseData['mention']);
        }
    }

    protected function parseEquationData(): void
    {
        if (Arr::exists($this->responseData, 'equation')) {
            $equationData = $this->responseData['equation'];
            $this->equationExpression = $equationData['expression'] ?? '';
        }
    }

    /**
     * Get the type of this rich text item (text, mention, or equation).
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Check if this is a text type rich text item.
     *
     * @return bool
     */
    public function isText(): bool
    {
        return $this->type === 'text';
    }

    /**
     * Check if this is a mention type rich text item.
     *
     * @return bool
     */
    public function isMention(): bool
    {
        return $this->type === 'mention';
    }

    /**
     * Check if this is an equation type rich text item.
     *
     * @return bool
     */
    public function isEquation(): bool
    {
        return $this->type === 'equation';
    }

    /**
     * Get the plain text without any formatting.
     *
     * @return string
     */
    public function getPlainText(): string
    {
        return $this->plainText;
    }

    /**
     * Get the href/URL if this rich text item contains a link or mention.
     *
     * @return string|null
     */
    public function getHref(): ?string
    {
        return $this->href;
    }

    /**
     * Check if this rich text item has a link.
     *
     * @return bool
     */
    public function hasLink(): bool
    {
        return $this->href !== null;
    }

    /**
     * Get the annotations/styling for this rich text item.
     *
     * @return Annotation
     */
    public function getAnnotations(): Annotation
    {
        return $this->annotations;
    }

    /**
     * Get text content (for text type items).
     *
     * @return string|null
     */
    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    /**
     * Get text link data (for text type items).
     * Returns an array with 'url' key if link exists, null otherwise.
     *
     * @return array|null
     */
    public function getTextLink(): ?array
    {
        return $this->textLink;
    }

    /**
     * Check if text item has an inline link.
     *
     * @return bool
     */
    public function hasTextLink(): bool
    {
        return $this->textLink !== null;
    }

    /**
     * Get the URL from text link if it exists.
     *
     * @return string|null
     */
    public function getTextLinkUrl(): ?string
    {
        if ($this->hasTextLink()) {
            return $this->textLink['url'] ?? null;
        }
        return null;
    }

    /**
     * Get mention data (for mention type items).
     *
     * @return RichTextMention|null
     */
    public function getMention(): ?RichTextMention
    {
        return $this->mention;
    }

    /**
     * Get equation expression (for equation type items).
     *
     * @return string|null
     */
    public function getEquationExpression(): ?string
    {
        return $this->equationExpression;
    }

    /**
     * Convert to string - returns plain text.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getPlainText();
    }

    private static function normalizeAnnotations(array $annotations): array
    {
        return array_merge([
            'bold' => false,
            'italic' => false,
            'strikethrough' => false,
            'underline' => false,
            'code' => false,
            'color' => 'default',
        ], $annotations);
    }
}

