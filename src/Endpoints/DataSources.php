<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Collections\DataSourceCollection;
use Jensvandewiel\LaravelNotionApi\Entities\DataSource;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Exceptions\NotionException;

/**
 * Class DataSources.
 *
 * Data sources endpoint for Notion API 2025-09-03+.
 * This endpoint allows querying and managing data sources (databases) within Notion.
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
     * Query a data source to retrieve pages.
     *
     * @url https://api.notion.com/{version}/data_sources/{data_source_id}/query
     *
     * @reference https://developers.notion.com/reference/post-data-source-query
     *
     * @param  string  $dataSourceId
     * @param  array  $payload
     * @return array
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function query(string $dataSourceId, array $payload = []): array
    {
        $response = $this->post(
            $this->url(self::DATA_SOURCES."/{$dataSourceId}/query"),
            $payload
        );

        return $response->json();
    }

    /**
     * List data sources for a specific database.
     *
     * This is useful for multi-source databases to discover all available data sources.
     *
     * @param  string  $databaseId
     * @return DataSourceCollection
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function forDatabase(string $databaseId): DataSourceCollection
    {
        $payload = [
            'filter' => [
                'property' => 'database_id',
                'text' => [
                    'equals' => $databaseId,
                ],
            ],
        ];

        $resultData = $this->post(
            $this->url(self::DATA_SOURCES.'/query'),
            $payload
        )->json();

        return new DataSourceCollection($resultData);
    }
}

