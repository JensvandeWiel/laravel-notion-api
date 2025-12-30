<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Token;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Exceptions\NotionException;

/**
 * Class Tokens.
 *
 * Tokens endpoint for Notion API OAuth.
 */
class Tokens extends Endpoint
{
    /**
     * Create a token.
     *
     * @url https://api.notion.com/{version}/oauth/token (post)
     *
     * @reference https://developers.notion.com/reference/create-a-token
     *
     * @param  array  $payload
     * @return Token
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function create(array $payload): Token
    {
        $result = $this
            ->post($this->url(Endpoint::TOKENS.'/token'), $payload);

        return new Token($result->json());
    }

    /**
     * Introspect a token.
     *
     * @url https://api.notion.com/{version}/oauth/introspect (post)
     *
     * @reference https://developers.notion.com/reference/introspect-token
     *
     * @param  string  $token
     * @return array
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function introspect(string $token): array
    {
        $result = $this
            ->post($this->url(Endpoint::TOKENS.'/introspect'), ['token' => $token]);

        return $result->json();
    }

    /**
     * Refresh a token.
     *
     * @url https://api.notion.com/{version}/oauth/token (post)
     *
     * @reference https://developers.notion.com/reference/refresh-a-token
     *
     * @param  string  $refreshToken
     * @return Token
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function refresh(string $refreshToken): Token
    {
        $result = $this
            ->post($this->url(Endpoint::TOKENS.'/token'), [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        return new Token($result->json());
    }

    /**
     * Revoke a token.
     *
     * @url https://api.notion.com/{version}/oauth/revoke (post)
     *
     * @reference https://developers.notion.com/reference/revoke-token
     *
     * @param  string  $token
     * @return bool
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function revoke(string $token): bool
    {
        $this
            ->post($this->url(Endpoint::TOKENS.'/revoke'), ['token' => $token]);

        return true;
    }
}
