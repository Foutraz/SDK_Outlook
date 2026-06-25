<?php

namespace Foutraz\Outlook\Actions;

use Foutraz\Outlook\Dto\TokenResponse;
use Foutraz\Outlook\Exceptions\ActionFailed;
use Foutraz\Outlook\Exceptions\InvalidData;
use Foutraz\Outlook\Exceptions\ResourceNotFound;
use Foutraz\Outlook\Exceptions\TooManyRequestsException;
use Foutraz\Outlook\Exceptions\Unauthorized;
use Foutraz\Outlook\OutlookManager;
use GuzzleHttp\Exception\GuzzleException;

class ManagesAuthentication extends OutlookManager
{
    /**
     * @param  array<int, string>  $scopes
     */
    public function authorizeUrl(array $scopes = ['Calendars.Read', 'offline_access', 'User.Read']): string
    {
        return "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/authorize?".http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'response_mode' => 'query',
            'scope' => implode(' ', $scopes),
        ]);
    }

    /**
     * @throws ActionFailed
     * @throws GuzzleException
     * @throws InvalidData
     * @throws ResourceNotFound
     * @throws TooManyRequestsException
     * @throws Unauthorized
     */
    public function exchangeToken(string $code): TokenResponse
    {
        return TokenResponse::fromArray($this->post("https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ]));
    }

    /**
     * @throws ActionFailed
     * @throws GuzzleException
     * @throws InvalidData
     * @throws ResourceNotFound
     * @throws TooManyRequestsException
     * @throws Unauthorized
     */
    public function refreshToken(string $refreshToken): TokenResponse
    {
        return TokenResponse::fromArray($this->post("https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]));
    }
}
