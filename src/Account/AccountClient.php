<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
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
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\NetworkException;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\RestErrorResponse;

final class AccountClient
{
    private const BASE_PATH = '/public/partner/pec/v3/accounts';

    public function __construct(private readonly ClientInterface $httpClient)
    {
    }

    public function create(AccountCreateRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH, $request->toArray());
    }

    public function checkAvailability(AccountAvailableRequest $request): AccountAvailableResponse
    {
        $response = $this->post(self::BASE_PATH . '/accountAvailable', ['json' => $request->toArray()]);

        return $this->mapAccountAvailableResponse($response);
    }

    public function cancellation(AccountCancellationRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/cancellation', $request->toArray());
    }

    public function undoCancellation(AccountCancellationUndoRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/cancellation-undo', $request->toArray());
    }

    public function changeRenewalType(AccountChangeRenewalTypeRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/changeRenewalType', $request->toArray());
    }

    public function changeType(AccountChangeTypeRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/changeType', $request->toArray());
    }

    public function changeExtraSize(AccountChangeExtraSizeRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/extraSize', $request->toArray());
    }

    public function info(AccountInfoRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/info', $request->toArray());
    }

    public function renew(AccountRenewRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/renew', $request->toArray());
    }

    public function search(AccountSearchRequest $request, ?AccountSearchOptions $options = null): AccountSearchResponse
    {
        $response = $this->post(
            self::BASE_PATH . '/search',
            [
                'json' => $request->toArray(),
                'query' => $options?->toQuery() ?? [],
            ]
        );

        return $this->mapAccountSearchResponse($response);
    }

    public function suspend(AccountSuspendRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/suspend', $request->toArray());
    }

    public function undoSuspend(AccountSuspendUndoRequest $request): AccountInfoResponse
    {
        return $this->postForAccountInfo(self::BASE_PATH . '/suspend-undo', $request->toArray());
    }

    public function types(): AccountTypesResponse
    {
        $response = $this->request('GET', self::BASE_PATH . '/types');

        return $this->mapAccountTypesResponse($response);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function postForAccountInfo(string $uri, array $payload): AccountInfoResponse
    {
        $response = $this->post($uri, ['json' => $payload]);

        return $this->mapAccountInfoResponse($response);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function post(string $uri, array $options): ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->httpClient->request($method, $uri, $options);
        } catch (GuzzleException $exception) {
            throw new NetworkException('Unable to communicate with the ArubaPEC API.', $exception);
        }
    }

    private function mapAccountInfoResponse(ResponseInterface $response): AccountInfoResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return AccountInfoResponse::fromArray($decoded);
    }

    private function mapAccountAvailableResponse(ResponseInterface $response): AccountAvailableResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return AccountAvailableResponse::fromArray($decoded);
    }

    private function mapAccountSearchResponse(ResponseInterface $response): AccountSearchResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return AccountSearchResponse::fromArray($decoded);
    }

    private function mapAccountTypesResponse(ResponseInterface $response): AccountTypesResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return AccountTypesResponse::fromArray($decoded);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if ($body === '') {
            throw new UnexpectedResponseException('Empty response body received from ArubaPEC API.');
        }

        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            throw new UnexpectedResponseException('Unable to decode JSON response from ArubaPEC API.');
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $decoded
     */
    private function throwIfErrorResponse(ResponseInterface $response, array $decoded): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode < 400) {
            return;
        }

        $errorResponse = RestErrorResponse::fromArray($decoded);

        throw ApiException::fromErrorResponse($statusCode, $errorResponse);
    }
}
