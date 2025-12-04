<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Collections;

use Jensvandewiel\LaravelNotionApi\Entities\DataSource;
use Illuminate\Support\Collection;

/**
 * Class DataSourceCollection.
 */
class DataSourceCollection extends EntityCollection
{
    protected function collectChildren(): void
    {
        $this->collection = new Collection();
        foreach ($this->rawResults as $dataSourceChild) {
            $this->collection->add(new DataSource($dataSourceChild));
        }
    }
}

