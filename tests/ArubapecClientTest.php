<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shellrent\Arubapec\AdditionalService\AdditionalServiceClient;
use Shellrent\Arubapec\ArubapecClient;
use Shellrent\Arubapec\Auth\AuthClient;
use Shellrent\Arubapec\Country\CountryClient;

class ArubapecClientTest extends TestCase
{
    public function testClientCanBeInstantiated(): void
    {
        $client = new ArubapecClient();

        self::assertInstanceOf(ArubapecClient::class, $client);
        self::assertInstanceOf(AuthClient::class, $client->auth());
        self::assertInstanceOf(AdditionalServiceClient::class, $client->additionalService());
        self::assertInstanceOf(CountryClient::class, $client->country());
    }

    public function testCustomHttpClientCanBeInjected(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'version' => 'v2',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client([
            'handler' => $handlerStack,
            'http_errors' => false,
            'base_uri' => 'https://example.test',
        ]);

        $client = new ArubapecClient($httpClient);

        $client->auth()->refresh(new \Shellrent\Arubapec\Auth\Dto\RefreshRequest('token'));

        self::assertSame($httpClient, $client->getHttpClient());
    }
}
