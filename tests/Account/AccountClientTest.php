<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Tests\Account;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shellrent\Arubapec\Account\AccountClient;
use Shellrent\Arubapec\Account\Dto\AccountAvailableRequest;
use Shellrent\Arubapec\Account\Dto\AccountAvailableResponse;
use Shellrent\Arubapec\Account\Dto\AccountCancellationRequest;
use Shellrent\Arubapec\Account\Dto\AccountCancellationUndoRequest;
use Shellrent\Arubapec\Account\Dto\AccountChangeExtraSizeRequest;
use Shellrent\Arubapec\Account\Dto\AccountChangeRenewalTypeRequest;
use Shellrent\Arubapec\Account\Dto\AccountChangeTypeRequest;
use Shellrent\Arubapec\Account\Dto\AccountCreateRequest;
use Shellrent\Arubapec\Account\Dto\AccountInfoRequest;
use Shellrent\Arubapec\Account\Dto\AccountInfoResponse;
use Shellrent\Arubapec\Account\Dto\AccountRenewRequest;
use Shellrent\Arubapec\Account\Dto\AccountSearchOptions;
use Shellrent\Arubapec\Account\Dto\AccountSearchRequest;
use Shellrent\Arubapec\Account\Dto\AccountSearchResponse;
use Shellrent\Arubapec\Account\Dto\AccountSuspendRequest;
use Shellrent\Arubapec\Account\Dto\AccountSuspendUndoRequest;
use Shellrent\Arubapec\Account\Dto\AccountTypesResponse;
use Shellrent\Arubapec\Account\Dto\Interval;
use Shellrent\Arubapec\Account\Dto\OwnerId;
use Shellrent\Arubapec\Account\Dto\OwnerSearchRequest;
use Shellrent\Arubapec\Shared\Dto\RenewalData;
use Shellrent\Arubapec\Exception\ApiException;

final class AccountClientTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: object, 2: string}>
     */
    public static function accountInfoEndpointProvider(): array
    {
        $ownerId = new OwnerId(123);
        $renewal = new RenewalData('T', 1);

        return [
            'create' => ['create', new AccountCreateRequest('pec@example.com', 'EMAIL', $ownerId, $renewal), '/public/partner/pec/v3/accounts'],
            'cancellation' => ['cancellation', new AccountCancellationRequest('pec@example.com'), '/public/partner/pec/v3/accounts/cancellation'],
            'undo cancellation' => ['undoCancellation', new AccountCancellationUndoRequest('pec@example.com'), '/public/partner/pec/v3/accounts/cancellation-undo'],
            'change renewal type' => ['changeRenewalType', new AccountChangeRenewalTypeRequest('pec@example.com', 'T'), '/public/partner/pec/v3/accounts/changeRenewalType'],
            'change type' => ['changeType', new AccountChangeTypeRequest('pec@example.com', 'EMAIL_PREMIUM'), '/public/partner/pec/v3/accounts/changeType'],
            'change extra size' => ['changeExtraSize', new AccountChangeExtraSizeRequest('pec@example.com'), '/public/partner/pec/v3/accounts/extraSize'],
            'info' => ['info', new AccountInfoRequest('pec@example.com'), '/public/partner/pec/v3/accounts/info'],
            'renew' => ['renew', new AccountRenewRequest('pec@example.com', $renewal), '/public/partner/pec/v3/accounts/renew'],
            'suspend' => ['suspend', new AccountSuspendRequest('pec@example.com'), '/public/partner/pec/v3/accounts/suspend'],
            'undo suspend' => ['undoSuspend', new AccountSuspendUndoRequest('pec@example.com'), '/public/partner/pec/v3/accounts/suspend-undo'],
        ];
    }

    /**
     * @dataProvider accountInfoEndpointProvider
     */
    public function testAccountInfoEndpointsReturnResponse(string $method, object $request, string $expectedPath): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->buildAccountInfoPayload(), JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new Client(['handler' => $handlerStack, 'http_errors' => false]);
        $accountClient = new AccountClient($client);

        $response = $accountClient->{$method}($request);

        self::assertInstanceOf(AccountInfoResponse::class, $response);
        self::assertArrayHasKey(0, $history);
        self::assertSame($expectedPath, $history[0]['request']->getUri()->getPath());
    }

    public function testCheckAvailabilityParsesBooleanFlags(): void
    {
        $payload = [
            'version' => 'v3',
            'data' => [
                'accountExists' => true,
                'isAccountAssignable' => false,
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);

        $accountClient = new AccountClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $response = $accountClient->checkAvailability(new AccountAvailableRequest('pec', 'pec.aruba.it'));

        self::assertInstanceOf(AccountAvailableResponse::class, $response);
        self::assertFalse($response->getData()?->isAccountAssignable());
    }

    public function testSearchUsesProvidedFilters(): void
    {
        $payload = [
            'version' => 'v3',
            'data' => [
                'content' => [],
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new Client(['handler' => $handlerStack, 'http_errors' => false]);
        $accountClient = new AccountClient($client);

        $request = new AccountSearchRequest(
            new OwnerSearchRequest(taxCode: 'AAABBB00A00A000A'),
            new Interval(),
            null,
            'EMAIL',
            'ATTIVO'
        );

        $options = new AccountSearchOptions(page: 1, size: 20, sort: ['type,asc', 'status,desc']);

        $response = $accountClient->search($request, $options);

        self::assertInstanceOf(AccountSearchResponse::class, $response);
        self::assertArrayHasKey(0, $history);
        /** @var Request $httpRequest */
        $httpRequest = $history[0]['request'];
        parse_str($httpRequest->getUri()->getQuery(), $query);
        self::assertSame('1', $query['page']);
        self::assertSame('20', $query['size']);
        self::assertSame(['type,asc', 'status,desc'], $query['sort']);

        $decodedBody = json_decode((string) $httpRequest->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('EMAIL', $decodedBody['type']);
        self::assertSame('ATTIVO', $decodedBody['status']);
        self::assertSame('AAABBB00A00A000A', $decodedBody['owner']['taxCode']);
    }

    public function testTypesReturnsCollection(): void
    {
        $payload = [
            'version' => 'v3',
            'data' => [
                [
                    'type' => 'EMAIL',
                    'description' => 'Standard',
                    'inboxBase' => 2,
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);

        $accountClient = new AccountClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $response = $accountClient->types();

        self::assertInstanceOf(AccountTypesResponse::class, $response);
        self::assertCount(1, $response->getData());
    }

    public function testErrorResponseThrowsApiException(): void
    {
        $error = [
            'version' => 'v3',
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

        $accountClient = new AccountClient(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]));

        $this->expectException(ApiException::class);
        $accountClient->create(new AccountCreateRequest('pec@example.com', 'EMAIL', new OwnerId(123), new RenewalData('T', 1)));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAccountInfoPayload(): array
    {
        return [
            'version' => 'v3',
            'data' => [
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
                    'contacts' => [
                        'address' => 'Via Roma 1',
                        'town' => 'Arezzo',
                        'zipCode' => '52100',
                        'district' => 'AR',
                        'country' => 260,
                        'email' => 'owner@example.com',
                        'telephoneNumber' => '+39.000000000',
                    ],
                ],
                'requestDate' => '2023-09-07T12:09:36+02:00',
                'certificationDate' => '2023-09-07T12:19:36+02:00',
                'endDate' => '2024-09-07T12:09:36+02:00',
                'renewalData' => [
                    'type' => 'T',
                    'duration' => 1,
                ],
            ],
        ];
    }
}
