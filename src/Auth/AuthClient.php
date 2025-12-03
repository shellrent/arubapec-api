<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Auth;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Shellrent\Arubapec\Auth\Dto\RefreshRequest;
use Shellrent\Arubapec\Auth\Dto\TokenRequest;
use Shellrent\Arubapec\Auth\Dto\TokenResponse;
use Shellrent\Arubapec\Exception\ApiException;
use Shellrent\Arubapec\Exception\NetworkException;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\RestErrorResponse;

final class AuthClient
{
    private const TOKEN_ENDPOINT = '/service/public/auth/v2/token';
    private const REFRESH_ENDPOINT = '/service/public/auth/v2/refresh';

    public function __construct(private readonly ClientInterface $httpClient)
    {
    }

    public function token(TokenRequest $request): TokenResponse
    {
        return $this->requestToken(self::TOKEN_ENDPOINT, $request->toArray());
    }

    public function refresh(RefreshRequest $request): TokenResponse
    {
        return $this->requestToken(self::REFRESH_ENDPOINT, $request->toArray());
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function requestToken(string $uri, array $payload): TokenResponse
    {
        try {
            $response = $this->httpClient->request('POST', $uri, [
                'json' => $payload,
            ]);
        } catch (GuzzleException $exception) {
            throw new NetworkException('Unable to communicate with the ArubaPEC API.', $exception);
        }

        return $this->handleTokenResponse($response);
    }

    private function handleTokenResponse(ResponseInterface $response): TokenResponse
    {
        $decoded = $this->decodeResponse($response);
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            $errorResponse = RestErrorResponse::fromArray($decoded);

            throw ApiException::fromErrorResponse($statusCode, $errorResponse);
        }

        return TokenResponse::fromArray($decoded);
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
}
