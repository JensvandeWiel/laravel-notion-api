<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Properties;

use Jensvandewiel\LaravelNotionApi\Entities\Contracts\Modifiable;
use Jensvandewiel\LaravelNotionApi\Entities\User;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Illuminate\Support\Collection;

/**
 * Class People.
 */
class People extends Property implements Modifiable
{
    /**
     * @param  $userIds
     * @return People
     */
    public static function value(array $userIds): People
    {
        $peopleProperty = new People();
        $peopleProperty->content = new Collection();
        $peopleProperty->rawContent = [];

        foreach ($userIds as $userId) {
            array_push($peopleProperty->rawContent, ['object' => 'user', 'id' => $userId]);
            $peopleProperty->content->add(new User(['object' => 'user', 'id' => $userId]));
        }

        return $peopleProperty;
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

        $this->content = new Collection();
        foreach ($this->rawContent as $peopleItem) {
            $this->content->add(new User($peopleItem));
        }
    }

    /**
     * @return Collection
     */
    public function getContent(): Collection
    {
        return $this->getPeople();
    }

    /**
     * @return Collection
     */
    public function getPeople(): Collection
    {
        return $this->content;
    }
}
