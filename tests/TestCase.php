<?php

namespace Foutraz\Outlook\Tests;

use Foutraz\Outlook\OutlookManager;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $history = [];

    /**
     * Builds an OutlookManager whose Guzzle client replays the queued responses.
     *
     * @param  array<int, Response>  $responses
     */
    protected function managerWithResponses(array $responses): OutlookManager
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($this->history));

        $client = new Client([
            'handler' => $stack,
            'http_errors' => false,
            'base_uri' => 'https://graph.microsoft.com/v1.0/',
        ]);

        return new OutlookManager(
            'https://graph.microsoft.com/v1.0',
            'access-token',
            'client-id',
            'client-secret',
            'https://example.test/callback',
            'common',
            $client,
        );
    }

    /**
     * Builds a JSON-bodied Guzzle response.
     *
     * @param  array<mixed>  $body
     */
    protected function jsonResponse(int $status, array $body): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], (string) json_encode($body));
    }

    protected function lastRequestUri(): string
    {
        $request = end($this->history)['request'];

        return (string) $request->getUri();
    }
}
