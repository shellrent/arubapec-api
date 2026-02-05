<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Shellrent\Arubapec\Domain\Dto\DomainBoolResponse;
use Shellrent\Arubapec\Domain\Dto\DomainByNameRequest;
use Shellrent\Arubapec\Domain\Dto\DomainCertifyRequest;
use Shellrent\Arubapec\Domain\Dto\DomainDataResponse;
use Shellrent\Arubapec\Domain\Dto\DomainInfoRequest;
use Shellrent\Arubapec\Domain\Dto\DomainMailboxesResponse;
use Shellrent\Arubapec\Domain\Dto\DomainOwnerChangeRequest;
use Shellrent\Arubapec\Domain\Dto\DomainSearchRequest;
use Shellrent\Arubapec\Domain\Dto\DomainSearchResponse;
use Shellrent\Arubapec\Domain\Dto\DomainTypologyResponse;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\NetworkException;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\PageRequestOptions;
use Shellrent\Arubapec\Shared\Dto\RestErrorResponse;

final class DomainClient
{
    private const BASE_PATH = '/service/public/partner/pec/v2/domains';

    public function __construct(private readonly ClientInterface $httpClient)
    {
    }

    public function certify(DomainCertifyRequest $request): DomainDataResponse
    {
        return $this->postForDomainData(self::BASE_PATH, $request->toArray());
    }

    public function info(DomainInfoRequest $request): DomainDataResponse
    {
        return $this->postForDomainData(self::BASE_PATH . '/info', $request->toArray());
    }

    public function listMailboxes(DomainByNameRequest $request, ?PageRequestOptions $options = null): DomainMailboxesResponse
    {
        $response = $this->post(
            self::BASE_PATH . '/list-mailboxes',
            [
                'json' => $request->toArray(),
                'query' => $options?->toQuery() ?? [],
            ]
        );

        return $this->mapDomainMailboxesResponse($response);
    }

    public function cancellation(DomainByNameRequest $request): DomainBoolResponse
    {
        return $this->postForBool(self::BASE_PATH . '/cancellation', $request->toArray());
    }

    public function undoCancellation(DomainByNameRequest $request): DomainBoolResponse
    {
        return $this->postForBool(self::BASE_PATH . '/cancellation-undo', $request->toArray());
    }

    public function undoCertification(DomainByNameRequest $request): DomainBoolResponse
    {
        return $this->postForBool(self::BASE_PATH . '/certification-undo', $request->toArray());
    }

    public function ownerChange(DomainOwnerChangeRequest $request): DomainBoolResponse
    {
        return $this->postForBool(self::BASE_PATH . '/owner-change', $request->toArray());
    }

    public function search(DomainSearchRequest $request, ?PageRequestOptions $options = null): DomainSearchResponse
    {
        $response = $this->post(
            self::BASE_PATH . '/search',
            [
                'json' => $request->toArray(),
                'query' => $options?->toQuery() ?? [],
            ]
        );

        return $this->mapDomainSearchResponse($response);
    }

    public function verifyCertifiability(DomainByNameRequest $request): DomainBoolResponse
    {
        return $this->postForBool(self::BASE_PATH . '/verify-certifiability', $request->toArray());
    }
	
	public function typology(DomainByNameRequest $request): DomainTypologyResponse
    {
        return $this->postForTypology(self::BASE_PATH . '/typology', $request->toArray());
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function postForDomainData(string $uri, array $payload): DomainDataResponse
    {
        $response = $this->post($uri, ['json' => $payload]);

        return $this->mapDomainDataResponse($response);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function postForBool(string $uri, array $payload): DomainBoolResponse
    {
        $response = $this->post($uri, ['json' => $payload]);

        return $this->mapDomainBoolResponse($response);
    }
	
	/**
     * @param array<string, mixed> $payload
     */
    private function postForTypology(string $uri, array $payload): DomainTypologyResponse
    {
        $response = $this->post($uri, ['json' => $payload]);

        return $this->mapTypologyResponse($response);
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

    private function mapDomainDataResponse(ResponseInterface $response): DomainDataResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return DomainDataResponse::fromArray($decoded);
    }

    private function mapDomainMailboxesResponse(ResponseInterface $response): DomainMailboxesResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return DomainMailboxesResponse::fromArray($decoded);
    }

    private function mapDomainSearchResponse(ResponseInterface $response): DomainSearchResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return DomainSearchResponse::fromArray($decoded);
    }

    private function mapDomainBoolResponse(ResponseInterface $response): DomainBoolResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return DomainBoolResponse::fromArray($decoded);
    }
	
	private function mapTypologyResponse(ResponseInterface $response): DomainTypologyResponse
    {
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return DomainTypologyResponse::fromArray($decoded);
    }

    /**
     * @return array<string, mixed>
     */
	private function decodeResponse(ResponseInterface $response): array
	{
		$body = (string) $response->getBody();
		$statusCode = $response->getStatusCode();
		
		if ($body === '') {
			throw new UnexpectedResponseException('Empty response body received from ArubaPEC API.');
		}
		
		$decoded = json_decode($body, true);
		
		if (!is_array($decoded)) {
			
			if ($statusCode >= 400 or $statusCode <= 599) {
				throw new ApiException($response->getReasonPhrase(), $statusCode);
			}
			
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
