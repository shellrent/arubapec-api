<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Tests\Domain;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shellrent\Arubapec\Domain\DomainClient;
use Shellrent\Arubapec\Domain\Dto\DomainByNameRequest;
use Shellrent\Arubapec\Domain\Dto\DomainCertifyRequest;
use Shellrent\Arubapec\Domain\Dto\DomainInfoRequest;
use Shellrent\Arubapec\Domain\Dto\DomainOwnerChangeRequest;
use Shellrent\Arubapec\Domain\Dto\DomainSearchRequest;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Shared\Dto\PageRequestOptions;

final class DomainClientTest extends TestCase
{
    public function testCertifyReturnsDomainDataResponse(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildDomainDataPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new DomainClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $response = $client->certify(new DomainCertifyRequest('pec.example.com', 'DOMINIO_ARUBA', 10));

        self::assertSame('v2', $response->getVersion());
        self::assertNotNull($response->getData());
        self::assertSame('pec.example.com', $response->getData()?->getFullName());
        self::assertArrayHasKey(0, $history);
        self::assertSame('/public/partner/pec/v2/domains', $history[0]['request']->getUri()->getPath());
        self::assertSame('POST', $history[0]['request']->getMethod());
    }

    public function testInfoSendsPayload(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildDomainDataPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new DomainClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $client->info(new DomainInfoRequest(id: 25, loadExtraData: true));

        self::assertArrayHasKey(0, $history);
        /** @var Request $request */
        $request = $history[0]['request'];
        self::assertSame('/public/partner/pec/v2/domains/info', $request->getUri()->getPath());
        self::assertSame('POST', $request->getMethod());
        $decodedBody = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(25, $decodedBody['id']);
        self::assertTrue($decodedBody['loadExtraData']);
    }

    public function testListMailboxesSupportsQueryOptions(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildMailboxesPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new DomainClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));

        $options = new PageRequestOptions(page: 2, size: 25, sort: ['name,asc']);
        $client->listMailboxes(new DomainByNameRequest('pec.example.com'), $options);

        self::assertArrayHasKey(0, $history);
        /** @var Request $request */
        $request = $history[0]['request'];
        self::assertSame('/public/partner/pec/v2/domains/list-mailboxes', $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $query);
        self::assertSame('2', $query['page']);
        self::assertSame('25', $query['size']);
        self::assertSame(['name,asc'], $query['sort']);
    }

    /**
     * @return array<string, array{callable(DomainClient): void, string}>
     */
    public function lifecycleProvider(): array
    {
        $byNameRequest = new DomainByNameRequest('pec.example.com');
        $ownerChangeRequest = new DomainOwnerChangeRequest(newOwnerId: 33, domainId: 77);

        return [
            'cancellation' => [
                static function (DomainClient $client) use ($byNameRequest): void {
                    $client->cancellation($byNameRequest);
                },
                '/public/partner/pec/v2/domains/cancellation',
            ],
            'undo cancellation' => [
                static function (DomainClient $client) use ($byNameRequest): void {
                    $client->undoCancellation($byNameRequest);
                },
                '/public/partner/pec/v2/domains/cancellation-undo',
            ],
            'undo certification' => [
                static function (DomainClient $client) use ($byNameRequest): void {
                    $client->undoCertification($byNameRequest);
                },
                '/public/partner/pec/v2/domains/certification-undo',
            ],
            'owner change' => [
                static function (DomainClient $client) use ($ownerChangeRequest): void {
                    $client->ownerChange($ownerChangeRequest);
                },
                '/public/partner/pec/v2/domains/owner-change',
            ],
            'verify certifiability' => [
                static function (DomainClient $client) use ($byNameRequest): void {
                    $client->verifyCertifiability($byNameRequest);
                },
                '/public/partner/pec/v2/domains/verify-certifiability',
            ],
        ];
    }

    /**
     * @dataProvider lifecycleProvider
     */
    public function testLifecycleEndpoints(callable $operation, string $expectedPath): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildBoolPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new DomainClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $operation($client);

        self::assertArrayHasKey(0, $history);
        /** @var Request $request */
        $request = $history[0]['request'];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($expectedPath, $request->getUri()->getPath());
    }

    public function testSearchSerialisesPayloadAndQuery(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildSearchPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new DomainClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $request = new DomainSearchRequest(type: 'DOMINIO_ARUBA', status: 'CERTIFICATO');
        $options = new PageRequestOptions(page: 1, size: 10);
        $response = $client->search($request, $options);

        self::assertNotNull($response->getData());
        self::assertSame('v2', $response->getVersion());
        self::assertArrayHasKey(0, $history);
        /** @var Request $historyRequest */
        $historyRequest = $history[0]['request'];
        $decodedBody = json_decode((string) $historyRequest->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('DOMINIO_ARUBA', $decodedBody['type']);
        parse_str($historyRequest->getUri()->getQuery(), $query);
        self::assertSame('1', $query['page']);
        self::assertSame('10', $query['size']);
    }

    public function testErrorResponseThrowsApiException(): void
    {
        $errorPayload = [
            'version' => 'v2',
            'errors' => [
                [
                    'code' => 'ERR',
                    'description' => 'Something went wrong',
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(400, [], json_encode($errorPayload, JSON_THROW_ON_ERROR)),
        ]);

        $client = new DomainClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $this->expectException(ApiException::class);
        $client->certify(new DomainCertifyRequest('pec.example.com', 'DOMINIO_ARUBA', 10));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDomainDataPayload(): array
    {
        return [
            'version' => 'v2',
            'data' => [
                'fullName' => 'pec.example.com',
                'typology' => 'DOMINIO_ARUBA',
                'status' => 'CERTIFICATO',
                'requestDate' => '2023-09-07T12:09:36+02:00',
                'certificationDate' => '2023-09-07T12:19:36+02:00',
                'endDate' => '2024-09-07T12:09:36+02:00',
                'owner' => [
                    'userType' => 'PRIVATO',
                    'name' => 'Mario',
                    'surname' => 'Rossi',
                    'taxCode' => 'AAABBB00A00A000A',
                ],
                'contractData' => [
                    'sdiCode' => 'ABC1234',
                    'cigOda' => 'CIG-0001',
                ],
                'additionalServices' => [
                    [
                        'id' => 10,
                        'type' => 'NEWSLETTER',
                        'status' => 'ATTIVO',
                        'requestDate' => '2023-09-07T12:09:36+02:00',
                        'activationDate' => '2023-09-07T13:09:36+02:00',
                        'endDate' => '2024-09-07T12:09:36+02:00',
                        'renewalData' => [
                            'type' => 'T',
                            'duration' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildMailboxesPayload(): array
    {
        return [
            'version' => 'v2',
            'data' => [
                'totalElements' => 1,
                'content' => [
                    [
                        'name' => 'pec@example.com',
                        'type' => 'EMAIL',
                        'status' => 'ATTIVO',
                        'quotas' => [
                            [
                                'type' => 'INBOX',
                                'base' => 1,
                            ],
                        ],
                        'owner' => [
                            'userType' => 'PRIVATO',
                            'name' => 'Mario',
                            'surname' => 'Rossi',
                            'taxCode' => 'AAABBB00A00A000A',
                        ],
                        'requestDate' => '2023-09-07T12:09:36+02:00',
                        'certificationDate' => '2023-09-07T12:19:36+02:00',
                        'endDate' => '2024-09-07T12:09:36+02:00',
                        'renewalData' => [
                            'type' => 'T',
                            'duration' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSearchPayload(): array
    {
        $payload = $this->buildDomainDataPayload();
        $payload['data'] = [
            'totalElements' => 1,
            'content' => [$payload['data']],
        ];

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBoolPayload(): array
    {
        return [
            'version' => 'v2',
            'data' => true,
        ];
    }
}
