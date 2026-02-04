<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Partner;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Shellrent\Arubapec\Partner\Dto\PartnerResponse;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\NetworkException;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\RestErrorResponse;

final class PartnerClient
{
    private const BASE_PATH = '/service/public/partner/v1/partners';

    public function __construct(private readonly ClientInterface $httpClient)
    {
    }

    public function readPartnerCredit(): PartnerResponse
    {
        $response = $this->request('GET', self::BASE_PATH. '/readPartnerCredit');
        $decoded = $this->decodeResponse($response);
        $this->throwIfErrorResponse($response, $decoded);

        return PartnerResponse::fromArray($decoded);
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
