<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Auth\Dto;

final class TokenRequest
{
    public function __construct(
        private readonly string $username,
        private readonly string $password
    ) {
    }

    /**
     * @return array{username: string, password: string}
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
