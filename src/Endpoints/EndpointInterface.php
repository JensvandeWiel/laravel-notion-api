<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Entity;

/**
 * Interface EndpointInterface.
 */
interface EndpointInterface
{
    /**
     * @param  string  $id
     * @return Entity
     */
    public function find(string $id): Entity;
}
