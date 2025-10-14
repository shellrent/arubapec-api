<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Auth\Dto;

final class RefreshRequest
{
    public function __construct(private readonly string $refreshToken)
    {
    }

    /**
     * @return array{refreshToken: string}
     */
    public function toArray(): array
    {
        return [
            'refreshToken' => $this->refreshToken,
        ];
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
