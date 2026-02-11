<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\PropertyItems;

use Jensvandewiel\LaravelNotionApi\Entities\Entity;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class RichText.
 *
 * Represents a rich text property or block content.
 * Contains an array of RichTextItem objects, each representing a single styled text segment, mention, or equation.
 */
class RichText extends Entity
{
    /**
     * @var string
     */
    protected string $plainText = '';

    /**
     * @var Collection<RichTextItem>
     */
    protected Collection $items;

    public function __construct(array $responseData = null)
    {
        $this->items = new Collection();
        parent::__construct($responseData);
    }

    /**
     * @param  array  $responseData
     */
    protected function setResponseData(array $responseData): void
    {
        $this->responseData = $responseData;
        $this->fillFromRaw();
    }

    protected function fillFromRaw(): void
    {
        $this->parseRichTextItems();
        $this->fillPlainText();
    }

    /**
     * Parse the response data into RichTextItem objects.
     */
    protected function parseRichTextItems(): void
    {
        $this->items = new Collection();

        if (is_array($this->responseData)) {
            foreach ($this->responseData as $itemData) {
                if (is_array($itemData)) {
                    $this->items->push(new RichTextItem($itemData));
                }
            }
        }
    }

    /**
     * Extract plain text from all rich text items.
     */
    protected function fillPlainText(): void
    {
        $this->plainText = '';
        foreach ($this->items as $item) {
            $this->plainText .= $item->getPlainText();
        }
    }

    /**
     * Get all rich text items.
     *
     * @return Collection<RichTextItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Get a single rich text item by index.
     *
     * @param int $index
     * @return RichTextItem|null
     */
    public function getItem(int $index): ?RichTextItem
    {
        return $this->items->get($index);
    }

    /**
     * Get the number of rich text items.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Get the complete plain text without any formatting.
     * This maintains backward compatibility with existing code.
     *
     * @return string
     */
    public function getPlainText(): string
    {
        return $this->plainText;
    }

    /**
     * Set plain text (creates a simple text item).
     * This is used for modification/creation of text blocks.
     *
     * @param string $text
     * @return void
     */
    public function setPlainText(string $text): void
    {
        $this->plainText = $text;
        // Clear items and create a new one with the text
        $this->items = new Collection();
        $this->items->push(new RichTextItem([
            'type' => 'text',
            'text' => [
                'content' => $text,
                'link' => null,
            ],
            'annotations' => $this->normalizeAnnotations([]),
            'plain_text' => $text,
            'href' => null,
        ]));
    }

    /**
     * Create a RichText instance from a plain string.
     */
    public static function fromPlainText(string $text): self
    {
        $richText = new self();
        $richText->setPlainText($text);

        return $richText;
    }

    /**
     * Create a RichText instance from raw rich text items.
     */
    public static function fromItems(array $items): self
    {
        return new self($items);
    }

    /**
     * Append a text item with optional annotations and link.
     */
    public function addText(string $content, array $annotations = [], ?string $link = null): self
    {
        $item = [
            'type' => 'text',
            'text' => [
                'content' => $content,
                'link' => $link === null ? null : ['url' => $link],
            ],
            'annotations' => $this->normalizeAnnotations($annotations),
            'plain_text' => $content,
            'href' => $link,
        ];

        $this->items->push(new RichTextItem($item));
        $this->plainText .= $content;

        return $this;
    }

    /**
     * Export raw rich text items in Notion API format.
     */
    public function toRaw(): array
    {
        return $this->items
            ->map(function (RichTextItem $item) {
                return $item->getRawResponse();
            })
            ->values()
            ->all();
    }

    /**
     * Get all types found in this rich text.
    /**
     * @return Collection<string>
     */
    public function getTypes(): Collection
    {
        return $this->items->map(function (RichTextItem $item) {
            return $item->getType();
        })->unique();
    }

    /**
     * Check if any item has annotations applied.
     *
     * @return bool
     */
    public function hasAnnotations(): bool
    {
        return $this->items->some(function (RichTextItem $item) {
            return $item->getAnnotations()->hasAnyAnnotation();
        });
    }

    /**
     * Check if any item has a link.
     *
     * @return bool
     */
    public function hasLinks(): bool
    {
        return $this->items->some(function (RichTextItem $item) {
            return $item->hasLink();
        });
    }

    /**
     * Get all items that have links.
     *
     * @return Collection<RichTextItem>
     */
    public function getLinkedItems(): Collection
    {
        return $this->items->filter(function (RichTextItem $item) {
            return $item->hasLink();
        });
    }

    /**
     * Get all items of a specific type.
     *
     * @param string $type (text, mention, or equation)
     * @return Collection<RichTextItem>
     */
    public function getItemsByType(string $type): Collection
    {
        return $this->items->filter(function (RichTextItem $item) use ($type) {
            return $item->getType() === $type;
        });
    }

    /**
     * Get all text type items.
     *
     * @return Collection<RichTextItem>
     */
    public function getTextItems(): Collection
    {
        return $this->getItemsByType('text');
    }

    /**
     * Get all mention type items.
     *
     * @return Collection<RichTextItem>
     */
    public function getMentionItems(): Collection
    {
        return $this->getItemsByType('mention');
    }

    /**
     * Get all equation type items.
     *
     * @return Collection<RichTextItem>
     */
    public function getEquationItems(): Collection
    {
        return $this->getItemsByType('equation');
    }

    /**
     * Check if this rich text contains any mentions.
     *
     * @return bool
     */
    public function hasMentions(): bool
    {
        return $this->items->contains(function (RichTextItem $item) {
            return $item->isMention();
        });
    }

    /**
     * Check if this rich text contains any equations.
     *
     * @return bool
     */
    public function hasEquations(): bool
    {
        return $this->items->contains(function (RichTextItem $item) {
            return $item->isEquation();
        });
    }

    /**
     * Convert to string - returns plain text.
     * This maintains backward compatibility.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getPlainText();
    }

    private function normalizeAnnotations(array $annotations): array
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
