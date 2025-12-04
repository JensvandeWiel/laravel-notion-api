<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Collections\PageCollection;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Exceptions\NotionException;
use Jensvandewiel\LaravelNotionApi\Query\Filters\FilterBag;
use Jensvandewiel\LaravelNotionApi\Query\Sorting;
use Jensvandewiel\LaravelNotionApi\Query\StartCursor;
use Illuminate\Support\Collection;

/**
 * Class DataSource.
 *
 * Data source endpoint for querying pages from a specific data source.
 * Similar to the Database endpoint but uses data_source_id for identification.
 *
 * @reference https://developers.notion.com/reference/data-sources
 */
class DataSource extends Endpoint
{
    /**
     * @var string
     */
    private string $dataSourceId;

    /**
     * @var FilterBag|null
     */
    private ?FilterBag $filterBag = null;

    /**
     * @var Collection
     */
    private Collection $sortings;

    /**
     * DataSource constructor.
     *
     * @param  string  $dataSourceId
     * @param  \Jensvandewiel\LaravelNotionApi\Notion  $notion
     *
     * @throws HandlingException
     */
    public function __construct(string $dataSourceId, $notion)
    {
        parent::__construct($notion);
        $this->dataSourceId = $dataSourceId;
        $this->sortings = new Collection();
    }

    /**
     * Query the data source to retrieve pages.
     *
     * @url https://api.notion.com/{version}/data_sources/{data_source_id}/query
     *
     * @reference https://developers.notion.com/reference/post-data-source-query
     *
     * @return PageCollection
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function query(): PageCollection
    {
        $payload = [];

        if ($this->filterBag !== null) {
            $payload['filter'] = $this->filterBag->toQuery();
        }

        if ($this->sortings->count() > 0) {
            $payload['sorts'] = $this->sortings->map(fn (Sorting $sorting) => $sorting->toArray())->toArray();
        }

        if ($this->startCursor !== null) {
            $payload['start_cursor'] = (string) $this->startCursor;
        }

        if ($this->pageSize !== 100) {
            $payload['page_size'] = $this->pageSize;
        }

        $response = $this->post(
            $this->url("data_sources/{$this->dataSourceId}/query"),
            $payload
        );

        return new PageCollection($response->json());
    }

    /**
     * Add a filter to the query.
     *
     * @param  FilterBag  $filterBag
     * @return DataSource
     */
    public function where(FilterBag $filterBag): DataSource
    {
        $this->filterBag = $filterBag;

        return $this;
    }

    /**
     * Add sorting to the query.
     *
     * @param  Sorting  $sorting
     * @return DataSource
     */
    public function sort(Sorting $sorting): DataSource
    {
        $this->sortings->add($sorting);

        return $this;
    }

    /**
     * Set the start cursor for pagination.
     *
     * @param  StartCursor  $startCursor
     * @return DataSource
     */
    public function startAt(StartCursor $startCursor): DataSource
    {
        $this->startCursor = $startCursor;

        return $this;
    }

    /**
     * Set the page size for results.
     *
     * @param  int  $pageSize
     * @return DataSource
     */
    public function pageSize(int $pageSize): DataSource
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * Get the data source ID.
     */
    public function getDataSourceId(): string
    {
        return $this->dataSourceId;
    }
}

