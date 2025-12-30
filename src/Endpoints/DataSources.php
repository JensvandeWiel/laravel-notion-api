<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Collections\DataSourceCollection;
use Jensvandewiel\LaravelNotionApi\Entities\Collections\TemplateCollection;
use Jensvandewiel\LaravelNotionApi\Entities\DataSource;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Exceptions\NotionException;

/**
 * Class DataSources.
 *
 * Data sources endpoint for Notion API 2025-09-03+.
 * This endpoint allows listing and retrieving data sources within Notion.
 *
 * For querying pages from a data source, use the DataSource endpoint instead:
 * @see DataSource
 *
 * @reference https://developers.notion.com/reference/data-sources
 */
class DataSources extends Endpoint implements EndpointInterface
{
    public const DATA_SOURCES = 'data_sources';

    /**
     * List data sources.
     *
     * @url https://api.notion.com/{version}/data_sources
     *
     * @reference https://developers.notion.com/reference/get-data-sources
     *
     * @return DataSourceCollection
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function all(): DataSourceCollection
    {
        $resultData = $this->getJson($this->url(self::DATA_SOURCES)."?{$this->buildPaginationQuery()}");

        return new DataSourceCollection($resultData);
    }

    /**
     * Retrieve a data source.
     *
     * @url https://api.notion.com/{version}/data_sources/{data_source_id}
     *
     * @reference https://developers.notion.com/reference/retrieve-a-data-source
     *
     * @param  string  $dataSourceId
     * @return DataSource
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function find(string $dataSourceId): DataSource
    {
        $result = $this->getJson($this->url(self::DATA_SOURCES."/{$dataSourceId}"));

        return new DataSource($result);
    }

    /**
     * Create a data source.
     *
     * @url https://api.notion.com/{version}/data_sources (post)
     *
     * @reference https://developers.notion.com/reference/create-a-data-source
     *
     * @param  array  $payload
     * @return DataSource
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function create(array $payload): DataSource
    {
        $result = $this
            ->post($this->url(self::DATA_SOURCES), $payload);

        return new DataSource($result->json());
    }

    /**
     * Update a data source.
     *
     * @url https://api.notion.com/{version}/data_sources/{data_source_id} (patch)
     *
     * @reference https://developers.notion.com/reference/update-a-data-source
     *
     * @param  string  $dataSourceId
     * @param  array  $payload
     * @return DataSource
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function update(string $dataSourceId, array $payload): DataSource
    {
        $result = $this
            ->patch($this->url(self::DATA_SOURCES."/{$dataSourceId}"), $payload);

        return new DataSource($result->json());
    }

    /**
     * Update data source properties.
     *
     * @url https://api.notion.com/{version}/data_sources/{data_source_id}/properties (patch)
     *
     * @reference https://developers.notion.com/reference/update-data-source-properties
     *
     * @param  string  $dataSourceId
     * @param  array  $properties
     * @return DataSource
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function updateProperties(string $dataSourceId, array $properties): DataSource
    {
        $result = $this
            ->patch($this->url(self::DATA_SOURCES."/{$dataSourceId}/properties"), $properties);

        return new DataSource($result->json());
    }

    /**
     * List data source templates.
     *
     * @url https://api.notion.com/{version}/data_sources/{data_source_id}/templates
     *
     * @reference https://developers.notion.com/reference/list-data-source-templates
     *
     * @param  string  $dataSourceId
     * @return TemplateCollection
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function listTemplates(string $dataSourceId): TemplateCollection
    {
        $resultData = $this->getJson($this->url(self::DATA_SOURCES."/{$dataSourceId}/templates")."?{$this->buildPaginationQuery()}");

        return new TemplateCollection($resultData);
    }
}
