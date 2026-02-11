<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Page;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Exceptions\NotionException;

/**
 * Class Pages.
 */
class Pages extends Endpoint implements EndpointInterface
{
    /**
     * Retrieve a page
     * url: https://api.notion.com/{version}/pages/{page_id}
     * notion-api-docs: https://developers.notion.com/reference/get-page.
     *
     * @param  string  $pageId
     * @return Page
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function find(string $pageId): Page
    {
        $response = $this->get(
            $this->url(Endpoint::PAGES.'/'.$pageId)
        );

        return new Page($response->json());
    }

    /**
     * @return Page
     * @throws HandlingException
     * @throws NotionException
     */
    public function createInDatabase(string $parentId, Page $page): Page
    {
        return $this->createIn($parentId, $page, 'database_id');
    }

    /**
     * @return Page
     * @throws HandlingException
     * @throws NotionException
     */
    public function createInPage(string $parentId, Page $page): Page
    {
        return $this->createIn($parentId, $page, 'page_id');
    }

    /**
     * Create a page in a parent page, database or data source.
     * @param string $parentId
     * @param Page   $page
     * @param string $parentType
     * @return Page
     * @throws HandlingException
     * @throws NotionException
     */
    public function createIn(string $parentId, Page $page, string $parentType): Page
    {
        $postData = [];
        $properties = [];

        foreach ($page->getProperties() as $property) {
            $properties[$property->getTitle()] = $property->getRawContent();
        }

        $postData['parent'] = [
            $parentType => $parentId,
            'type'      => $parentType,
        ];
        $postData['properties'] = $properties;

        $response = $this
            ->post(
                $this->url(Endpoint::PAGES),
                $postData
            )
            ->json();

        return new Page($response);
    }

    /**
     * Create a page in a data source.
     *
     * @return Page
     */
    public function createInDataSource(string $dataSourceId, Page $page): Page
    {
        return $this->createIn($dataSourceId, $page, 'data_source_id');
    }

    /**
     * @return array
     * @throws HandlingException
     */
    public function update(Page $page): Page
    {
        $postData = [];
        $properties = [];

        foreach ($page->getProperties() as $property) {
            $properties[$property->getTitle()] = $property->getRawContent();
        }

        $postData['properties'] = $properties;

        $response = $this
            ->patch(
                $this->url(Endpoint::PAGES.'/'.$page->getId()),
                $postData
            )
            ->json();

        return new Page($response);
    }

    /**
     * Archive a page.
     *
     * @url https://api.notion.com/{version}/pages/{page_id} (patch)
     *
     * @reference https://developers.notion.com/reference/archive-a-page
     *
     * @param  string  $pageId
     * @return Page
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function archive(string $pageId): Page
    {
        $response = $this
            ->patch(
                $this->url(Endpoint::PAGES.'/'.$pageId),
                ['archived' => true]
            )
            ->json();

        return new Page($response);
    }

    /**
     * Move a page.
     *
     * @url https://api.notion.com/{version}/pages/{page_id}/move (post)
     *
     * @reference https://developers.notion.com/reference/move-page
     *
     * @param  string  $pageId
     * @param  array  $payload
     * @return Page
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function move(string $pageId, array $payload): Page
    {
        $response = $this
            ->post(
                $this->url(Endpoint::PAGES.'/'.$pageId.'/move'),
                $payload
            )
            ->json();

        return new Page($response);
    }
}
