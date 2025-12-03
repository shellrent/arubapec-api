<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Owner;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\NetworkException;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Owner\Dto\OwnerCreateRequest;
use Shellrent\Arubapec\Owner\Dto\OwnerInfoResponse;
use Shellrent\Arubapec\Owner\Dto\OwnerSearchOptions;
use Shellrent\Arubapec\Owner\Dto\OwnerSearchResponse;
use Shellrent\Arubapec\Owner\Dto\OwnerUpdateRequest;
use Shellrent\Arubapec\Shared\Dto\OwnerSearchRequest;
use Shellrent\Arubapec\Shared\Dto\RestErrorResponse;

final class OwnerClient
{
    private const BASE_PATH = '/service/public/partner/pec/v2/owners';

    public function __construct(private readonly ClientInterface $httpClient)
    {
    }

    public function create(OwnerCreateRequest $request): OwnerInfoResponse
    {
        return $this->sendForOwnerInfo('POST', self::BASE_PATH, ['json' => $request->toArray()]);
    }

    public function update(OwnerUpdateRequest $request): OwnerInfoResponse
    {
        return $this->sendForOwnerInfo('PATCH', self::BASE_PATH, ['json' => $request->toArray()]);
    }

    public function info(int $ownerId): OwnerInfoResponse
    {
        $response = $this->request('GET', sprintf('%s/%d', self::BASE_PATH, $ownerId));

        return $this->mapOwnerInfoResponse($response);
    }

    public function search(OwnerSearchRequest $request, ?OwnerSearchOptions $options = null): OwnerSearchResponse
    {
        $response = $this->request(
            'POST',
            self::BASE_PATH . '/search',
            [
                'json' => $request->toArray(),
                'query' => $options?->toQuery() ?? [],
            ]
        );

        return $this->mapOwnerSearchResponse($response);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function sendForOwnerInfo(string $method, string $uri, array $options): OwnerInfoResponse
    {
        $response = $this->request($method, $uri, $options);

        return $this->mapOwnerInfoResponse($response);
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

    private function mapOwnerInfoResponse(ResponseInterface $response): OwnerInfoResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return OwnerInfoResponse::fromArray($decoded);
    }

    private function mapOwnerSearchResponse(ResponseInterface $response): OwnerSearchResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return OwnerSearchResponse::fromArray($decoded);
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
