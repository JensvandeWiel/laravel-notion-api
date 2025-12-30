<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Properties;

use Jensvandewiel\LaravelNotionApi\Entities\Contracts\Modifiable;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;

/**
 * Class Checkbox.
 */
class Checkbox extends Property implements Modifiable
{
    /**
     * @param  $checked
     * @return Checkbox
     */
    public static function value(bool $checked): Checkbox
    {
        $checkboxProperty = new Checkbox();
        $checkboxProperty->content = $checked;

        $checkboxProperty->rawContent = $checkboxProperty->isChecked();

        return $checkboxProperty;
    }

    /**
     * @throws HandlingException
     */
    protected function fillFromRaw(): void
    {
        parent::fillFromRaw();
        $this->content = $this->rawContent;
    }

    /**
     * @return bool|null
     */
    public function getContent(): ?bool
    {
        return $this->content;
    }

    /**
     * @return bool|null
     */
    public function isChecked(): ?bool
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function asText(): string
    {
        return ($this->getContent() === true) ? 'true' : 'false';
    }
}
