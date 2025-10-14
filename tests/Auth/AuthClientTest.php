<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Tests\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shellrent\Arubapec\Auth\AuthClient;
use Shellrent\Arubapec\Auth\Dto\RefreshRequest;
use Shellrent\Arubapec\Auth\Dto\TokenRequest;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class AuthClientTest extends TestCase
{
    public function testTokenRequestIsParsed(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'version' => 'v2',
                'datetime' => '2023-09-19T09:35:10.415+02:00',
                'data' => [
                    'accessToken' => 'access-token',
                    'expiresIn' => 300,
                    'refreshToken' => 'refresh-token',
                    'refreshExpiresIn' => 43200,
                    'tokenType' => 'Bearer',
                ],
            ])),
        ]);

        $authClient = $this->createClient($mock);

        $response = $authClient->token(new TokenRequest('user@example.test', 'password'));

        self::assertNotNull($response->getData());
        self::assertSame('access-token', $response->getData()->getAccessToken());
        self::assertSame('Bearer', $response->getData()->getTokenType());
        self::assertSame(300, $response->getData()->getExpiresIn());
        self::assertNotNull($response->getDatetime());
    }

    public function testRefreshThrowsApiExceptionOnError(): void
    {
        $mock = new MockHandler([
            new Response(401, ['Content-Type' => 'application/json'], json_encode([
                'version' => 'v2',
                'errors' => [
                    [
                        'code' => '401',
                        'description' => 'Unauthorized access',
                    ],
                ],
            ])),
        ]);

        $authClient = $this->createClient($mock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Unauthorized access');

        $authClient->refresh(new RefreshRequest('refresh-token'));
    }

    public function testInvalidJsonThrowsUnexpectedResponseException(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], 'not-json'),
        ]);

        $authClient = $this->createClient($mock);

        $this->expectException(UnexpectedResponseException::class);

        $authClient->token(new TokenRequest('user', 'password'));
    }

    private function createClient(MockHandler $mockHandler): AuthClient
    {
        $handlerStack = HandlerStack::create($mockHandler);

        $httpClient = new Client([
            'handler' => $handlerStack,
            'http_errors' => false,
            'base_uri' => 'https://example.test',
        ]);

        return new AuthClient($httpClient);
    }
}
