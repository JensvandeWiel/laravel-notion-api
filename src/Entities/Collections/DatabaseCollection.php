<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Collections;

use Jensvandewiel\LaravelNotionApi\Entities\Database;
use Illuminate\Support\Collection;

/**
 * Class DatabaseCollection.
 */
class DatabaseCollection extends EntityCollection
{
    protected function collectChildren(): void
    {
        $this->collection = new Collection();
        foreach ($this->rawResults as $databaseChild) {
            $this->collection->add(new Database($databaseChild));
        }
    }
}
