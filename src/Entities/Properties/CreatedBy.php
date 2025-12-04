<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Properties;

use Jensvandewiel\LaravelNotionApi\Entities\User;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;

/**
 * Class CreatedBy.
 */
class CreatedBy extends Property
{
    /**
     * @throws HandlingException
     */
    protected function fillFromRaw(): void
    {
        parent::fillFromRaw();
        if (! is_array($this->rawContent)) {
            throw HandlingException::instance('The property-type is created_by, however the raw data-structure does not reprecent this type (= array of items). Please check the raw response-data.');
        }

        $this->content = new User($this->rawContent);
    }

    /**
     * @return User
     */
    public function getContent(): User
    {
        return $this->getUser();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->content;
    }
}
