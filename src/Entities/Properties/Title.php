<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Properties;

use Jensvandewiel\LaravelNotionApi\Entities\Contracts\Modifiable;
use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\RichText;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;

/**
 * Class Title.
 */
class Title extends Property implements Modifiable
{
    /**
     * @var string
     */
    protected string $plainText = '';

    /**
     * @param  $text
     * @return Title
     */
    public static function value($text): Title
    {
        $titleProperty = new Title();

        if (is_string($text)) {
            $richText = new RichText();
            $richText->setPlainText($text);
            $titleProperty->plainText = $richText->getPlainText();
            $titleProperty->content = $richText;
        } else {
            $titleProperty->plainText = $text->getPlainText();
            $titleProperty->content = $text;
        }

        //!INFO: Currently only plain_text is transfered into rawContent
        //TODO: Later the RichText has to return it's raw structure into 'content'
        $titleProperty->rawContent = [
            [
                'type' => 'text',
                'text' => [
                    'content' => $richText->getPlainText(),
                ],
                'annotations' => [
                    'bold' => false,
                    'italic' => false,
                    'strikethrough' => false,
                    'underline' => false,
                    'code' => false,
                    'color' => 'default',
                ],
                'plain_text' => $richText->getPlainText(),
                'href' => null,
            ],
        ];

        return $titleProperty;
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

    private function fillText(): void
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
