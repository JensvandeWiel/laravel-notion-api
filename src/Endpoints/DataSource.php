<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Collections\PageCollection;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Exceptions\NotionException;
use Jensvandewiel\LaravelNotionApi\Query\Filters\Filter;
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
     * @var Filter|null
     */
    private ?Filter $filter = null;

    /**
     * @var FilterBag|null
     */
    private ?FilterBag $filterBag = null;

    /**
     * @var array
     */
    private array $filterData = [];

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
        $response = $this->post(
            $this->url("data_sources/{$this->dataSourceId}/query"),
            $this->getPostData()
        )->json();

        return new PageCollection($response);
    }

    /**
     * Get the POST data for the query request.
     * Ensures payload is always an object (never empty array).
     */
    public function getPostData(): array
    {
        $postData = [];

        if ($this->sortings->isNotEmpty()) {
            $postData['sorts'] = Sorting::sortQuery($this->sortings);
        }

        if ($this->filter !== null && ! is_null($this->filterBag)) {
            throw new HandlingException('Please provide either a filter bag or a single filter.');
        } elseif ($this->filter !== null || ! is_null($this->filterBag)) {
            $postData['filter'] = $this->filterData;
        }

        if ($this->startCursor !== null) {
            $postData['start_cursor'] = $this->startCursor->__toString();
        }

        if ($this->pageSize !== null) {
            $postData['page_size'] = $this->pageSize;
        }

        return $postData;
    }

    /**
     * Add a filter to the query.
     *
     * @param  Collection|Filter|FilterBag  $filter
     * @return DataSource
     *
     * @throws HandlingException
     */
    public function filterBy(Collection|Filter|FilterBag $filter): DataSource
    {
        if ($filter instanceof Collection) {
            return $this->filterByCollection($filter);
        }
        if ($filter instanceof FilterBag) {
            return $this->filterByBag($filter);
        }
        if ($filter instanceof Filter) {
            return $this->filterBySingleFilter($filter);
        }

        return $this;
    }

    /**
     * Filter by a single Filter instance.
     *
     * @param  Filter  $filter
     * @return DataSource
     *
     * @throws HandlingException
     */
    public function filterBySingleFilter(Filter $filter): DataSource
    {
        $this->filter = $filter;
        $this->filterData = ['or' => [$filter->toQuery()]];

        return $this;
    }

    /**
     * Filter by a FilterBag instance.
     *
     * @param  FilterBag  $filterBag
     * @return DataSource
     */
    public function filterByBag(FilterBag $filterBag): DataSource
    {
        $this->filterBag = $filterBag;
        $this->filterData = $filterBag->toQuery();

        return $this;
    }

    /**
     * Filter by a Collection of filters.
     *
     * @param  Collection  $filterCollection
     * @return DataSource
     *
     * @throws HandlingException
     */
    public function filterByCollection(Collection $filterCollection): DataSource
    {
        $this->filterData = ['and' => Filter::filterQuery($filterCollection)];

        return $this;
    }

    /**
     * Alias for filterBy() for backward compatibility and convenience.
     *
     * @param  FilterBag  $filterBag
     * @return DataSource
     */
    public function where(FilterBag $filterBag): DataSource
    {
        return $this->filterByBag($filterBag);
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

