<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\PropertyItems;

use Jensvandewiel\LaravelNotionApi\Entities\Entity;

/**
 * Class Annotation.
 *
 * Represents the annotations/styling applied to a rich text object.
 */
class Annotation extends Entity
{
    /**
     * @var bool
     */
    protected bool $bold = false;

    /**
     * @var bool
     */
    protected bool $italic = false;

    /**
     * @var bool
     */
    protected bool $strikethrough = false;

    /**
     * @var bool
     */
    protected bool $underline = false;

    /**
     * @var bool
     */
    protected bool $code = false;

    /**
     * @var string
     */
    protected string $color = 'default';

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
        $this->bold = $this->responseData['bold'] ?? false;
        $this->italic = $this->responseData['italic'] ?? false;
        $this->strikethrough = $this->responseData['strikethrough'] ?? false;
        $this->underline = $this->responseData['underline'] ?? false;
        $this->code = $this->responseData['code'] ?? false;
        $this->color = $this->responseData['color'] ?? 'default';
    }

    /**
     * @return bool
     */
    public function isBold(): bool
    {
        return $this->bold;
    }

    /**
     * @return bool
     */
    public function isItalic(): bool
    {
        return $this->italic;
    }

    /**
     * @return bool
     */
    public function isStrikethrough(): bool
    {
        return $this->strikethrough;
    }

    /**
     * @return bool
     */
    public function isUnderline(): bool
    {
        return $this->underline;
    }

    /**
     * @return bool
     */
    public function isCode(): bool
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Check if any annotation is applied.
     *
     * @return bool
     */
    public function hasAnyAnnotation(): bool
    {
        return $this->bold || $this->italic || $this->strikethrough ||
               $this->underline || $this->code || $this->color !== 'default';
    }
}

