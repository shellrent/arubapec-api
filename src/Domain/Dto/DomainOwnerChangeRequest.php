<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

use InvalidArgumentException;

final class DomainOwnerChangeRequest
{
    public function __construct(
        private readonly int $newOwnerId,
        private readonly ?int $domainId = null,
        private readonly ?string $fullName = null
    ) {
        if ($this->domainId === null && ($this->fullName === null || $this->fullName === '')) {
            throw new InvalidArgumentException('Either the domain id or full name must be provided.');
        }
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        $payload = [
            'newOwnerId' => $this->newOwnerId,
        ];

        if ($this->domainId !== null) {
            $payload['domainId'] = $this->domainId;
        }

        if ($this->fullName !== null) {
            $payload['fullName'] = $this->fullName;
        }

        return $payload;
    }

    public function getNewOwnerId(): int
    {
        return $this->newOwnerId;
    }

    public function getDomainId(): ?int
    {
        return $this->domainId;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }
}
