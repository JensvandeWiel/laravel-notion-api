<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Collections;

use Illuminate\Support\Collection;

/**
 * Class TemplateCollection.
 */
class TemplateCollection
{
    /**
     * @var array
     */
    protected array $responseData = [];

    /**
     * @var array
     */
    protected array $rawResults = [];

    /**
     * @var bool
     */
    protected bool $hasMore = false;

    /**
     * @var string
     */
    protected ?string $nextCursor = null;

    /**
     * @var Collection
     */
    protected Collection $collection;

    /**
     * TemplateCollection constructor.
     *
     * @param  array|null  $responseData
     */
    public function __construct(array $responseData = null)
    {
        $this->setResponseData($responseData);
    }

    /**
     * @param  array  $responseData
     */
    protected function setResponseData(array $responseData): void
    {
        $this->responseData = $responseData;
        $this->fillFromRaw();
        $this->collectChildren();
    }

    protected function fillFromRaw(): void
    {
        $this->rawResults = $this->responseData['templates'] ?? [];
        $this->hasMore = $this->responseData['has_more'] ?? false;
        $this->nextCursor = $this->responseData['next_cursor'] ?? null;
    }

    protected function collectChildren(): void
    {
        $this->collection = new Collection();
        foreach ($this->rawResults as $template) {
            $this->collection->add($template); // Templates are simple objects, not entities
        }
    }

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->collection;
    }

    /**
     * @return bool
     */
    public function hasMore(): bool
    {
        return $this->hasMore;
    }

    /**
     * @return string|null
     */
    public function nextCursor(): ?string
    {
        return $this->nextCursor;
    }
}
