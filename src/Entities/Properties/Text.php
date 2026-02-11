<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Properties;

use Jensvandewiel\LaravelNotionApi\Entities\Contracts\Modifiable;
use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\RichText;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;

/**
 * Class Text.
 */
class Text extends Property implements Modifiable
{
    /**
     * @var string
     */
    protected string $plainText = '';

    /**
     * @param  $text
     * @return Text
     */
    public static function value($text): Text
    {
        $textProperty = new Text();

        $richText = is_string($text) ? RichText::fromPlainText($text) : $text;

        $textProperty->plainText = $richText->getPlainText();
        $textProperty->content = $richText;
        $textProperty->rawContent = $richText->toRaw();

        return $textProperty;
    }

    /**
     * Explicit helper for RichText input.
     */
    public static function fromRichText(RichText $text): Text
    {
        return self::value($text);
    }

    /**
     * @throws HandlingException
     */
    protected function fillFromRaw(): void
    {
        parent::fillFromRaw();

        // Handle null or empty rawContent
        if ($this->rawContent === null || !is_array($this->rawContent)) {
            return;
        }

        $this->fillText();
    }

    protected function fillText(): void
    {
        $this->content = new RichText($this->rawContent);
        $this->plainText = $this->content->getPlainText();
    }

    /**
     * @return RichText
     */
    public function getContent(): RichText
    {
        return $this->getRichText();
    }

    /**
     * @return string
     */
    public function asText(): string
    {
        return $this->getPlainText();
    }

    /**
     * @return RichText
     */
    public function getRichText(): RichText
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getPlainText(): string
    {
        return $this->plainText;
    }
}
