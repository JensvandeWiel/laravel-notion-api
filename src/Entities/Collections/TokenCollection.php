<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Collections;

use Jensvandewiel\LaravelNotionApi\Entities\Token;
use Illuminate\Support\Collection;

/**
 * Class TokenCollection.
 */
class TokenCollection extends EntityCollection
{
    protected function collectChildren(): void
    {
        $this->collection = new Collection();
        foreach ($this->rawResults as $tokenChild) {
            $this->collection->add(new Token($tokenChild));
        }
    }
}
