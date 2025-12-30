<?php

namespace Jensvandewiel\LaravelNotionApi\Entities;

use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Illuminate\Support\Arr;

/**
 * Class Token.
 *
 * Represents a Notion Token object.
 */
class Token extends Entity
{
    /**
     * @var string
     */
    protected string $accessToken = '';

    /**
     * @var string|null
     */
    protected ?string $refreshToken = null;

    /**
     * @var string
     */
    protected string $tokenType = '';

    /**
     * @var int
     */
    protected int $expiresIn = 0;

    /**
     * @var string|null
     */
    protected ?string $scope = null;

    /**
     * @var string|null
     */
    protected ?string $owner = null;

    /**
     * @throws HandlingException
     */
    protected function setResponseData(array $responseData): void
    {
        parent::setResponseData($responseData);
        if ($responseData['object'] !== 'token') {
            throw HandlingException::instance('invalid json-array: the given object is not a token');
        }
        $this->fillFromRaw();
    }

    private function fillFromRaw(): void
    {
        parent::fillEssentials();
        $this->fillAccessToken();
        $this->fillRefreshToken();
        $this->fillTokenType();
        $this->fillExpiresIn();
        $this->fillScope();
        $this->fillOwner();
    }

    private function fillAccessToken(): void
    {
        if (Arr::exists($this->responseData, 'access_token')) {
            $this->accessToken = $this->responseData['access_token'];
        }
    }

    private function fillRefreshToken(): void
    {
        if (Arr::exists($this->responseData, 'refresh_token')) {
            $this->refreshToken = $this->responseData['refresh_token'];
        }
    }

    private function fillTokenType(): void
    {
        if (Arr::exists($this->responseData, 'token_type')) {
            $this->tokenType = $this->responseData['token_type'];
        }
    }

    private function fillExpiresIn(): void
    {
        if (Arr::exists($this->responseData, 'expires_in')) {
            $this->expiresIn = $this->responseData['expires_in'];
        }
    }

    private function fillScope(): void
    {
        if (Arr::exists($this->responseData, 'scope')) {
            $this->scope = $this->responseData['scope'];
        }
    }

    private function fillOwner(): void
    {
        if (Arr::exists($this->responseData, 'owner')) {
            $this->owner = $this->responseData['owner'];
        }
    }

    /**
     * Get the access token.
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get the refresh token.
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * Get the token type.
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * Get the expires in.
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * Get the scope.
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * Get the owner.
     */
    public function getOwner(): ?string
    {
        return $this->owner;
    }
}
