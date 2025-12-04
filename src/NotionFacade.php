<?php

namespace Jensvandewiel\LaravelNotionApi;

use Illuminate\Support\Facades\Facade;
use Jensvandewiel\LaravelNotionApi\Endpoints\Block;
use Jensvandewiel\LaravelNotionApi\Endpoints\Comments;
use Jensvandewiel\LaravelNotionApi\Endpoints\Database;
use Jensvandewiel\LaravelNotionApi\Endpoints\Databases;
use Jensvandewiel\LaravelNotionApi\Endpoints\DataSource;
use Jensvandewiel\LaravelNotionApi\Endpoints\DataSources;
use Jensvandewiel\LaravelNotionApi\Endpoints\Pages;
use Jensvandewiel\LaravelNotionApi\Endpoints\Resolve;
use Jensvandewiel\LaravelNotionApi\Endpoints\Search;
use Jensvandewiel\LaravelNotionApi\Endpoints\Users;

/**
 * Class NotionFacade.
 *
 * @method static Databases databases() Get the databases endpoint
 * @method static Database database(string $databaseId) Get a specific database
 * @method static DataSources dataSources() Get the data sources endpoint (2025-09-03+)
 * @method static DataSource dataSource(string $dataSourceId) Get a specific data source (2025-09-03+)
 * @method static Pages pages() Get the pages endpoint
 * @method static Block block(string $blockId) Get a specific block
 * @method static Users users() Get the users endpoint
 * @method static Search search(string $searchText = '') Search in workspace
 * @method static Comments comments() Get the comments endpoint
 * @method static Resolve resolve() Get the resolve endpoint
 * @method static Notion v1() Set version to v1 (2025-09-03)
 * @method static string getVersion() Get current API version
 * @method static mixed getConnection() Get the HTTP connection
 *
 * @see Notion
 */
class NotionFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Notion::class;
    }
}
