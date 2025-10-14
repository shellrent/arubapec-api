<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

final class AccountSuspendRequest
{
    public function __construct(private readonly string $name)
    {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }
}
