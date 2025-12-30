<?php

namespace Jensvandewiel\LaravelNotionApi\Entities;

use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Traits\HasTimestamps;
use Illuminate\Support\Arr;

/**
 * Class FileUpload.
 *
 * Represents a Notion File Upload object.
 */
class FileUpload extends Entity
{
    use HasTimestamps;

    /**
     * @var string
     */
    protected string $status = '';

    /**
     * @var string|null
     */
    protected ?string $expiryTime = null;

    /**
     * @var string|null
     */
    protected ?string $filename = null;

    /**
     * @var string|null
     */
    protected ?string $contentType = null;

    /**
     * @var int|null
     */
    protected ?int $contentLength = null;

    /**
     * @var string|null
     */
    protected ?string $uploadUrl = null;

    /**
     * @var string|null
     */
    protected ?string $completeUrl = null;

    /**
     * @var string|null
     */
    protected ?string $fileImportResult = null;

    /**
     * @throws HandlingException
     */
    protected function setResponseData(array $responseData): void
    {
        parent::setResponseData($responseData);
        if ($responseData['object'] !== 'file_upload') {
            throw HandlingException::instance('invalid json-array: the given object is not a file_upload');
        }
        $this->fillFromRaw();
    }

    private function fillFromRaw(): void
    {
        parent::fillEssentials();
        $this->fillStatus();
        $this->fillExpiryTime();
        $this->fillFilename();
        $this->fillContentType();
        $this->fillContentLength();
        $this->fillUploadUrl();
        $this->fillCompleteUrl();
        $this->fillFileImportResult();
    }

    private function fillStatus(): void
    {
        if (Arr::exists($this->responseData, 'status')) {
            $this->status = $this->responseData['status'];
        }
    }

    private function fillExpiryTime(): void
    {
        if (Arr::exists($this->responseData, 'expiry_time')) {
            $this->expiryTime = $this->responseData['expiry_time'];
        }
    }

    private function fillFilename(): void
    {
        if (Arr::exists($this->responseData, 'filename')) {
            $this->filename = $this->responseData['filename'];
        }
    }

    private function fillContentType(): void
    {
        if (Arr::exists($this->responseData, 'content_type')) {
            $this->contentType = $this->responseData['content_type'];
        }
    }

    private function fillContentLength(): void
    {
        if (Arr::exists($this->responseData, 'content_length')) {
            $this->contentLength = $this->responseData['content_length'];
        }
    }

    private function fillUploadUrl(): void
    {
        if (Arr::exists($this->responseData, 'upload_url')) {
            $this->uploadUrl = $this->responseData['upload_url'];
        }
    }

    private function fillCompleteUrl(): void
    {
        if (Arr::exists($this->responseData, 'complete_url')) {
            $this->completeUrl = $this->responseData['complete_url'];
        }
    }

    private function fillFileImportResult(): void
    {
        if (Arr::exists($this->responseData, 'file_import_result')) {
            $this->fileImportResult = $this->responseData['file_import_result'];
        }
    }

    /**
     * Get the file upload status.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get the expiry time.
     */
    public function getExpiryTime(): ?string
    {
        return $this->expiryTime;
    }

    /**
     * Get the filename.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Get the content type.
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * Get the content length.
     */
    public function getContentLength(): ?int
    {
        return $this->contentLength;
    }

    /**
     * Get the upload URL.
     */
    public function getUploadUrl(): ?string
    {
        return $this->uploadUrl;
    }

    /**
     * Get the complete URL.
     */
    public function getCompleteUrl(): ?string
    {
        return $this->completeUrl;
    }

    /**
     * Get the file import result.
     */
    public function getFileImportResult(): ?string
    {
        return $this->fileImportResult;
    }
}
