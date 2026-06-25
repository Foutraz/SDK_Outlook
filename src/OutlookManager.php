<?php

namespace Foutraz\Outlook;

use Foutraz\Outlook\Actions\ManagesAuthentication;
use Foutraz\Outlook\Actions\ManagesCalendars;
use Foutraz\Outlook\Actions\ManagesEvents;
use Foutraz\Outlook\Concerns\MakesHttpRequests;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class OutlookManager
{
    use MakesHttpRequests;

    public function __construct(
        public string $endpoint,
        public string $apiToken,
        public string $clientId,
        public string $clientSecret,
        public string $redirectUri,
        public string $tenant = 'common',
        public ?ClientInterface $client = null
    ) {
        $this->client ??= new Client([
            'http_errors' => false,
            'base_uri' => rtrim($this->endpoint, '/').'/',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->apiToken,
            ],
        ]);
    }

    public function auth(): ManagesAuthentication
    {
        return new ManagesAuthentication($this->endpoint, $this->apiToken, $this->clientId, $this->clientSecret, $this->redirectUri, $this->tenant, $this->client);
    }

    public function calendars(): ManagesCalendars
    {
        return new ManagesCalendars($this->endpoint, $this->apiToken, $this->clientId, $this->clientSecret, $this->redirectUri, $this->tenant, $this->client);
    }

    public function events(): ManagesEvents
    {
        return new ManagesEvents($this->endpoint, $this->apiToken, $this->clientId, $this->clientSecret, $this->redirectUri, $this->tenant, $this->client);
    }

    public function setToken(string $token): static
    {
        $this->apiToken = $token;

        $this->client = new Client([
            'http_errors' => false,
            'base_uri' => rtrim($this->endpoint, '/').'/',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return $this;
    }
}
