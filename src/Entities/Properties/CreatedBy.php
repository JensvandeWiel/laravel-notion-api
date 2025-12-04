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

        // Handle null or empty rawContent
        if ($this->rawContent === null || !is_array($this->rawContent)) {
            return;
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
