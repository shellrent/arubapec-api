<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Tests\Country;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shellrent\Arubapec\Country\CountryClient;
use Shellrent\Arubapec\Country\Dto\CountriesResponse;
use Shellrent\Arubapec\Country\Dto\CountryModel;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class CountryClientTest extends TestCase
{
    public function testCountriesReturnsResponse(): void
    {
        $payload = [
            'version' => 'v2',
            'data' => [
                [
                    'id' => 260,
                    'name' => 'Italia',
                ],
                [
                    'id' => 261,
                    'name' => 'Francia',
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);

        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new CountryClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));

        $response = $client->countries();

        self::assertInstanceOf(CountriesResponse::class, $response);
        self::assertCount(2, $response->getData());
        self::assertContainsOnlyInstancesOf(CountryModel::class, $response->getData());
        self::assertSame('Italia', $response->getData()[0]->getName());

        self::assertArrayHasKey(0, $history);
        /** @var Request $request */
        $request = $history[0]['request'];
        self::assertSame('/public/partner/v2/countries', $request->getUri()->getPath());
        self::assertSame('GET', $request->getMethod());
    }

    public function testErrorResponseThrowsApiException(): void
    {
        $error = [
            'version' => 'v2',
            'errors' => [
                [
                    'code' => 'ERR',
                    'description' => 'Unauthorized',
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(401, [], json_encode($error, JSON_THROW_ON_ERROR)),
        ]);

        $client = new CountryClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $this->expectException(ApiException::class);
        $client->countries();
    }

    public function testMissingVersionThrowsUnexpectedResponseException(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['data' => []], JSON_THROW_ON_ERROR)),
        ]);

        $client = new CountryClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $this->expectException(UnexpectedResponseException::class);
        $client->countries();
    }
}
