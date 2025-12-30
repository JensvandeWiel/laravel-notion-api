<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Collections\FileUploadCollection;
use Jensvandewiel\LaravelNotionApi\Entities\FileUpload;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Exceptions\NotionException;

/**
 * Class FileUploads.
 *
 * File uploads endpoint for Notion API.
 */
class FileUploads extends Endpoint implements EndpointInterface
{
    /**
     * Create a file upload.
     *
     * @url https://api.notion.com/{version}/file_uploads (post)
     *
     * @reference https://developers.notion.com/reference/create-a-file-upload
     *
     * @param  array  $payload
     * @return FileUpload
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function create(array $payload): FileUpload
    {
        $result = $this
            ->post($this->url(Endpoint::FILE_UPLOADS), $payload);

        return new FileUpload($result->json());
    }

    /**
     * Retrieve a file upload.
     *
     * @url https://api.notion.com/{version}/file_uploads/{upload_id}
     *
     * @reference https://developers.notion.com/reference/retrieve-a-file-upload
     *
     * @param  string  $uploadId
     * @return FileUpload
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function find(string $uploadId): FileUpload
    {
        $result = $this->getJson($this->url(Endpoint::FILE_UPLOADS."/{$uploadId}"));

        return new FileUpload($result);
    }

    /**
     * Complete a file upload.
     *
     * @url https://api.notion.com/{version}/file_uploads/{upload_id}/complete (post)
     *
     * @reference https://developers.notion.com/reference/complete-a-file-upload
     *
     * @param  string  $uploadId
     * @param  array  $payload
     * @return FileUpload
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function complete(string $uploadId, array $payload): FileUpload
    {
        $result = $this
            ->post($this->url(Endpoint::FILE_UPLOADS."/{$uploadId}/complete"), $payload);

        return new FileUpload($result->json());
    }

    /**
     * Send a file upload.
     *
     * @url https://api.notion.com/{version}/file_uploads/{upload_id}/send (post)
     *
     * @reference https://developers.notion.com/reference/send-a-file-upload
     *
     * @param  string  $uploadId
     * @param  array  $payload
     * @return FileUpload
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function send(string $uploadId, array $payload): FileUpload
    {
        $result = $this
            ->post($this->url(Endpoint::FILE_UPLOADS."/{$uploadId}/send"), $payload);

        return new FileUpload($result->json());
    }

    /**
     * List file uploads.
     *
     * @url https://api.notion.com/{version}/file_uploads
     *
     * @reference https://developers.notion.com/reference/list-file-uploads
     *
     * @return FileUploadCollection
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function all(): FileUploadCollection
    {
        $resultData = $this->getJson($this->url(Endpoint::FILE_UPLOADS)."?{$this->buildPaginationQuery()}");

        return new FileUploadCollection($resultData);
    }
}
