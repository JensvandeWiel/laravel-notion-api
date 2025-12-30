<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Properties;

use Jensvandewiel\LaravelNotionApi\Entities\Contracts\Modifiable;

/**
 * Class Number.
 */
class Number extends Property implements Modifiable
{
    /**
     * @var float|int
     */
    protected float $number = 0;

    public static function value(float $number): Number
    {
        $numberProperty = new Number();
        $numberProperty->number = $number;
        $numberProperty->content = $number;

        $numberProperty->rawContent = $number;

        return $numberProperty;
    }

    protected function fillFromRaw(): void
    {
        parent::fillFromRaw();
        $this->fillNumber();
    }

    protected function fillNumber(): void
    {
        $this->content = $this->rawContent;
    }

    /**
     * @return float|null
     */
    public function getContent(): ?float
    {
        return $this->content;
    }

    /**
     * @return float|null
     */
    public function getNumber(): ?float
    {
        return $this->content;
    }
}
