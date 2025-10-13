<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Tests\Owner;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\NetworkException;
use Shellrent\Arubapec\Owner\Dto\OwnerContactData;
use Shellrent\Arubapec\Owner\Dto\OwnerContactDataUpdate;
use Shellrent\Arubapec\Owner\Dto\OwnerCreateRequest;
use Shellrent\Arubapec\Owner\Dto\OwnerInfoResponse;
use Shellrent\Arubapec\Owner\Dto\OwnerSearchOptions;
use Shellrent\Arubapec\Owner\Dto\OwnerSearchResponse;
use Shellrent\Arubapec\Owner\Dto\OwnerUpdateRequest;
use Shellrent\Arubapec\Owner\OwnerClient;
use Shellrent\Arubapec\Shared\Dto\OwnerId;
use Shellrent\Arubapec\Shared\Dto\OwnerSearchRequest;

final class OwnerClientTest extends TestCase
{
    public function testCreateOwnerSendsPayload(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildOwnerInfoPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new OwnerClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $contactData = new OwnerContactData('Via Roma', 'Arezzo', '52100', 'AR', 'info@example.com', '+39.000000000');
        $request = new OwnerCreateRequest('PRIVATO', 'Mario', 'Rossi', 'AAABBB00A00A000A', contacts: $contactData);
        $response = $client->create($request);

        self::assertInstanceOf(OwnerInfoResponse::class, $response);
        self::assertArrayHasKey(0, $history);
        /** @var Request $requestSent */
        $requestSent = $history[0]['request'];
        self::assertSame('/public/partner/pec/v2/owners', $requestSent->getUri()->getPath());
        self::assertSame('POST', $requestSent->getMethod());
        $payload = json_decode((string) $requestSent->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('PRIVATO', $payload['userType']);
        self::assertSame('Mario', $payload['name']);
        self::assertSame('AAABBB00A00A000A', $payload['taxCode']);
        self::assertSame('Via Roma', $payload['contacts']['address']);
    }

    public function testUpdateOwnerSendsPayload(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildOwnerInfoPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new OwnerClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $update = new OwnerUpdateRequest(
            new OwnerId(123),
            newTaxCode: 'BBBAAA00A00A000A',
            contacts: new OwnerContactDataUpdate(email: 'new@example.com')
        );
        $client->update($update);

        self::assertArrayHasKey(0, $history);
        /** @var Request $request */
        $request = $history[0]['request'];
        self::assertSame('/public/partner/pec/v2/owners', $request->getUri()->getPath());
        self::assertSame('PATCH', $request->getMethod());
        $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(123, $payload['ownerId']['id']);
        self::assertSame('BBBAAA00A00A000A', $payload['newTaxCode']);
        self::assertSame('new@example.com', $payload['contacts']['email']);
    }

    public function testInfoReturnsOwnerDetails(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildOwnerInfoPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new OwnerClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $response = $client->info(77);

        self::assertSame('v2', $response->getVersion());
        self::assertSame(77, $response->getData()?->getId());
        self::assertArrayHasKey(0, $history);
        self::assertSame('/public/partner/pec/v2/owners/77', $history[0]['request']->getUri()->getPath());
        self::assertSame('GET', $history[0]['request']->getMethod());
    }

    public function testSearchSupportsPaginationOptions(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildOwnerSearchPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new OwnerClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $options = new OwnerSearchOptions(page: 1, size: 20, sort: ['name,asc']);
        $response = $client->search(new OwnerSearchRequest(name: 'Mario'), $options);

        self::assertInstanceOf(OwnerSearchResponse::class, $response);
        self::assertArrayHasKey(0, $history);
        /** @var Request $request */
        $request = $history[0]['request'];
        parse_str($request->getUri()->getQuery(), $query);
        self::assertSame('1', $query['page']);
        self::assertSame('20', $query['size']);
        self::assertSame(['name,asc'], $query['sort']);
        $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('Mario', $payload['name']);
    }

    public function testErrorResponseThrowsApiException(): void
    {
        $mock = new MockHandler([
            new Response(400, [], json_encode([
                'version' => 'v2',
                'errors' => [
                    ['code' => 'ERR', 'description' => 'Invalid'],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $client = new OwnerClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $this->expectException(ApiException::class);
        $client->create(new OwnerCreateRequest('PRIVATO', 'Mario', 'Rossi', 'AAABBB00A00A000A'));
    }

    public function testNetworkExceptionIsThrownOnTransportErrors(): void
    {
        $mock = new MockHandler([
            function (): void {
                throw new RequestException('boom', new Request('GET', '/owners'));
            },
        ]);

        $client = new OwnerClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $this->expectException(NetworkException::class);
        $client->info(1);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOwnerInfoPayload(): array
    {
        return [
            'version' => 'v2',
            'data' => [
                'id' => 77,
                'userType' => 'PRIVATO',
                'name' => 'Mario',
                'surname' => 'Rossi',
                'taxCode' => 'AAABBB00A00A000A',
                'contacts' => [
                    'address' => 'Via Roma',
                    'town' => 'Arezzo',
                    'zipCode' => '52100',
                    'district' => 'AR',
                    'country' => 260,
                    'email' => 'info@example.com',
                    'telephoneNumber' => '+39.000000000',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOwnerSearchPayload(): array
    {
        return [
            'version' => 'v2',
            'data' => [
                'totalElements' => 1,
                'totalPages' => 1,
                'size' => 20,
                'number' => 0,
                'numberOfElements' => 1,
                'first' => true,
                'last' => true,
                'empty' => false,
                'content' => [
                    $this->buildOwnerInfoPayload()['data'],
                ],
                'sort' => [],
            ],
        ];
    }
}
