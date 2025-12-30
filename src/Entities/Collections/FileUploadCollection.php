<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Collections;

use Jensvandewiel\LaravelNotionApi\Entities\FileUpload;
use Illuminate\Support\Collection;

/**
 * Class FileUploadCollection.
 */
class FileUploadCollection extends EntityCollection
{
    protected function collectChildren(): void
    {
        $this->collection = new Collection();
        foreach ($this->rawResults as $fileUploadChild) {
            $this->collection->add(new FileUpload($fileUploadChild));
        }
    }
}
