<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Tests\AdditionalService;

use Carbon\CarbonImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shellrent\Arubapec\AdditionalService\AdditionalServiceClient;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceCancellationRequest;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceCancellationUndoRequest;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceChangeRenewalTypeRequest;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceCreateRequest;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceInfoResponse;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceRenewRequest;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Shared\Dto\ContractData;
use Shellrent\Arubapec\Shared\Dto\RenewalData;

final class AdditionalServiceClientTest extends TestCase
{
    public function testCreateReturnsInfoResponse(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildInfoPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new AdditionalServiceClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));

        $request = new AdditionalServiceCreateRequest(
            'pec@example.com',
            'NEWSLETTER',
            new RenewalData('T', 1),
            value: '3'
        );

        $response = $client->create($request);

        self::assertInstanceOf(AdditionalServiceInfoResponse::class, $response);
        self::assertArrayHasKey(0, $history);
        self::assertSame('/public/partner/pec/v2/additionalServices', $history[0]['request']->getUri()->getPath());
        self::assertSame('POST', $history[0]['request']->getMethod());

        $decodedBody = json_decode((string) $history[0]['request']->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('pec@example.com', $decodedBody['account']);
        self::assertSame('3', $decodedBody['value']);
    }

    public function testInfoRequestsResource(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildInfoPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new AdditionalServiceClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));
        $client->info(123);

        self::assertArrayHasKey(0, $history);
        /** @var Request $request */
        $request = $history[0]['request'];
        self::assertSame('/public/partner/pec/v2/additionalServices/123', $request->getUri()->getPath());
        self::assertSame('GET', $request->getMethod());
    }

    /**
     * @return array<string, array{0: callable(AdditionalServiceClient): AdditionalServiceInfoResponse, 1: string, 2: string}>
     */
    public function lifecycleProvider(): array
    {
        $cancellationRequest = new AdditionalServiceCancellationRequest(
            10,
            'S',
            CarbonImmutable::parse('2024-01-01T00:00:00+00:00')
        );

        $undoRequest = new AdditionalServiceCancellationUndoRequest(10);

        $changeRenewalTypeRequest = new AdditionalServiceChangeRenewalTypeRequest(10, 'T');

        $renewRequest = new AdditionalServiceRenewRequest(
            10,
            new RenewalData('S', 2),
            new ContractData('SDI123', 'CIG123')
        );

        return [
            'cancellation' => [
                static fn (AdditionalServiceClient $client): AdditionalServiceInfoResponse => $client->cancellation($cancellationRequest),
                'PUT',
                '/public/partner/pec/v2/additionalServices/10/cancellation',
            ],
            'undo cancellation' => [
                static fn (AdditionalServiceClient $client): AdditionalServiceInfoResponse => $client->undoCancellation($undoRequest),
                'PUT',
                '/public/partner/pec/v2/additionalServices/10/cancellation-undo',
            ],
            'change renewal type' => [
                static fn (AdditionalServiceClient $client): AdditionalServiceInfoResponse => $client->changeRenewalType($changeRenewalTypeRequest),
                'PUT',
                '/public/partner/pec/v2/additionalServices/10/changeRenewalType',
            ],
            'renew' => [
                static fn (AdditionalServiceClient $client): AdditionalServiceInfoResponse => $client->renew($renewRequest),
                'PUT',
                '/public/partner/pec/v2/additionalServices/10/renew',
            ],
        ];
    }

    /**
     * @dataProvider lifecycleProvider
     */
    public function testLifecycleEndpoints(callable $operation, string $expectedMethod, string $expectedPath): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildInfoPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new AdditionalServiceClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));

        $operation($client);

        self::assertArrayHasKey(0, $history);
        /** @var Request $request */
        $request = $history[0]['request'];
        self::assertSame($expectedMethod, $request->getMethod());
        self::assertSame($expectedPath, $request->getUri()->getPath());
    }

    public function testLifecyclePayloadsAreSerialised(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildInfoPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new AdditionalServiceClient(new Client(['handler' => $handlerStack, 'http_errors' => false]));

        $cancellationRequest = new AdditionalServiceCancellationRequest(
            10,
            'D',
            CarbonImmutable::parse('2024-05-01T10:00:00+02:00')
        );

        $client->cancellation($cancellationRequest);

        $decodedBody = json_decode((string) $history[0]['request']->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('D', $decodedBody['type']);
        self::assertSame('2024-05-01T10:00:00+02:00', $decodedBody['cancellationDate']);
    }

    public function testErrorResponseThrowsApiException(): void
    {
        $error = [
            'version' => 'v2',
            'errors' => [
                [
                    'code' => 'ERR',
                    'description' => 'Invalid request',
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(400, [], json_encode($error, JSON_THROW_ON_ERROR)),
        ]);

        $client = new AdditionalServiceClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $this->expectException(ApiException::class);
        $client->create(new AdditionalServiceCreateRequest('pec@example.com', 'NEWSLETTER', new RenewalData('T', 1)));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildInfoPayload(): array
    {
        return [
            'version' => 'v2',
            'data' => [
                'id' => 10,
                'type' => 'NEWSLETTER',
                'status' => 'ATTIVO',
                'value' => '3',
                'requestDate' => '2024-01-10T12:00:00+01:00',
                'activationDate' => '2024-01-11T12:00:00+01:00',
                'renewalData' => [
                    'type' => 'T',
                    'duration' => 1,
                ],
            ],
        ];
    }
}
