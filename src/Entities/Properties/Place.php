<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Properties;

use Jensvandewiel\LaravelNotionApi\Entities\Contracts\Modifiable;

/**
 * Class Place.
 */
class Place extends Property implements Modifiable
{
    /**
     * @param  $name
     * @param  $lat
     * @param  $lon
     * @param  $address
     * @return Place
     */
    public static function value(string $name, float $lat, float $lon, string $address = null): Place
    {
        $placeProperty = new Place();
        $placeProperty->content = [
            'name' => $name,
            'lat' => $lat,
            'lon' => $lon,
            'address' => $address,
        ];

        $placeProperty->rawContent = [
            'name' => $name,
            'lat' => $lat,
            'lon' => $lon,
            'address' => $address,
        ];

        return $placeProperty;
    }

    protected function fillFromRaw(): void
    {
        parent::fillFromRaw();
        $this->fillPlace();
    }

    protected function fillPlace(): void
    {
        $this->content = $this->rawContent;
    }

    /**
     * @return array|null
     */
    public function getContent(): ?array
    {
        return $this->content;
    }

    /**
     * @return array|null
     */
    public function getPlace(): ?array
    {
        return $this->content;
    }
}
