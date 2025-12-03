<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\AdditionalService;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceCancellationRequest;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceCancellationUndoRequest;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceChangeRenewalTypeRequest;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceCreateRequest;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceInfoResponse;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceRenewRequest;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\NetworkException;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\RestErrorResponse;

final class AdditionalServiceClient
{
    private const BASE_PATH = '/service/public/partner/pec/v2/additionalServices';

    public function __construct(private readonly ClientInterface $httpClient)
    {
    }

    public function create(AdditionalServiceCreateRequest $request): AdditionalServiceInfoResponse
    {
        return $this->postForInfo(self::BASE_PATH, $request->toArray());
    }

    public function info(int $id): AdditionalServiceInfoResponse
    {
        $response = $this->request('GET', sprintf('%s/%d', self::BASE_PATH, $id));

        return $this->mapInfoResponse($response);
    }

    public function cancellation(AdditionalServiceCancellationRequest $request): AdditionalServiceInfoResponse
    {
        return $this->putForInfo(
            sprintf('%s/%d/cancellation', self::BASE_PATH, $request->getId()),
            $request->toArray()
        );
    }

    public function undoCancellation(AdditionalServiceCancellationUndoRequest $request): AdditionalServiceInfoResponse
    {
        return $this->putForInfo(
            sprintf('%s/%d/cancellation-undo', self::BASE_PATH, $request->getId()),
            $request->toArray()
        );
    }

    public function changeRenewalType(AdditionalServiceChangeRenewalTypeRequest $request): AdditionalServiceInfoResponse
    {
        return $this->putForInfo(
            sprintf('%s/%d/changeRenewalType', self::BASE_PATH, $request->getId()),
            $request->toArray()
        );
    }

    public function renew(AdditionalServiceRenewRequest $request): AdditionalServiceInfoResponse
    {
        return $this->putForInfo(
            sprintf('%s/%d/renew', self::BASE_PATH, $request->getId()),
            $request->toArray()
        );
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function postForInfo(string $uri, array $payload): AdditionalServiceInfoResponse
    {
        $response = $this->request('POST', $uri, ['json' => $payload]);

        return $this->mapInfoResponse($response);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function putForInfo(string $uri, array $payload): AdditionalServiceInfoResponse
    {
        $response = $this->request('PUT', $uri, [
            'json' => $payload === [] ? (object) [] : $payload,
        ]);

        return $this->mapInfoResponse($response);
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

    private function mapInfoResponse(ResponseInterface $response): AdditionalServiceInfoResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return AdditionalServiceInfoResponse::fromArray($decoded);
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
