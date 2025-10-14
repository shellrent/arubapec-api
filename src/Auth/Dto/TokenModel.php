<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Auth\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class TokenModel
{
    public function __construct(
        private readonly string $accessToken,
        private readonly int $expiresIn,
        private readonly string $refreshToken,
        private readonly int $refreshExpiresIn,
        private readonly string $tokenType
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        foreach (['accessToken', 'expiresIn', 'refreshToken', 'refreshExpiresIn', 'tokenType'] as $field) {
            if (!array_key_exists($field, $payload)) {
                throw new UnexpectedResponseException(sprintf('Missing `%s` in token payload.', $field));
            }
        }

        return new self(
            (string) $payload['accessToken'],
            (int) $payload['expiresIn'],
            (string) $payload['refreshToken'],
            (int) $payload['refreshExpiresIn'],
            (string) $payload['tokenType']
        );
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getRefreshExpiresIn(): int
    {
        return $this->refreshExpiresIn;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }
}
