<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

final class DomainByNameRequest
{
    public function __construct(private readonly string $fullName)
    {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return ['fullName' => $this->fullName];
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }
}
