<?php

namespace Jensvandewiel\LaravelNotionApi\Entities;

use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Traits\HasArchive;
use Jensvandewiel\LaravelNotionApi\Traits\HasTimestamps;
use Illuminate\Support\Arr;

/**
 * Class DataSource.
 *
 * Represents a Notion data source, which is a database within the Notion API 2025-09-03+.
 * Data sources can be part of multi-source databases or standalone.
 */
class DataSource extends Entity
{
    use HasTimestamps, HasArchive;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var string|null
     */
    protected ?string $databaseId = null;

    /**
     * @var string|null
     */
    protected ?string $workspaceId = null;

    /**
     * @var array
     */
    protected array $config = [];

    /**
     * @throws HandlingException
     */
    protected function setResponseData(array $responseData): void
    {
        parent::setResponseData($responseData);
        if ($responseData['object'] !== 'data_source') {
            throw HandlingException::instance('invalid json-array: the given object is not a data_source');
        }
        $this->fillFromRaw();
    }

    private function fillFromRaw(): void
    {
        parent::fillEssentials();
        $this->fillName();
        $this->fillType();
        $this->fillDatabaseId();
        $this->fillWorkspaceId();
        $this->fillConfig();
    }

    private function fillName(): void
    {
        if (Arr::exists($this->responseData, 'name')) {
            $this->name = $this->responseData['name'];
        }
    }

    private function fillType(): void
    {
        if (Arr::exists($this->responseData, 'type')) {
            $this->type = $this->responseData['type'];
        }
    }

    private function fillDatabaseId(): void
    {
        if (Arr::exists($this->responseData, 'database_id')) {
            $this->databaseId = $this->responseData['database_id'];
        }
    }

    private function fillWorkspaceId(): void
    {
        if (Arr::exists($this->responseData, 'workspace_id')) {
            $this->workspaceId = $this->responseData['workspace_id'];
        }
    }

    private function fillConfig(): void
    {
        if (Arr::exists($this->responseData, 'config')) {
            $this->config = $this->responseData['config'];
        }
    }

    /**
     * Get the data source name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the data source type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the database ID associated with this data source.
     */
    public function getDatabaseId(): ?string
    {
        return $this->databaseId;
    }

    /**
     * Get the workspace ID associated with this data source.
     */
    public function getWorkspaceId(): ?string
    {
        return $this->workspaceId;
    }

    /**
     * Get the configuration data.
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}

