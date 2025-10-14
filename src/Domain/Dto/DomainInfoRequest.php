<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

use InvalidArgumentException;

final class DomainInfoRequest
{
    public function __construct(
        private readonly ?int $id = null,
        private readonly ?string $fullName = null,
        private readonly ?bool $loadExtraData = null
    ) {
        if ($this->id === null && ($this->fullName === null || $this->fullName === '')) {
            throw new InvalidArgumentException('Either the domain id or full name must be provided.');
        }
    }

    /**
     * @return array<string, bool|int|string>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->id !== null) {
            $payload['id'] = $this->id;
        }

        if ($this->fullName !== null) {
            $payload['fullName'] = $this->fullName;
        }

        if ($this->loadExtraData !== null) {
            $payload['loadExtraData'] = $this->loadExtraData;
        }

        return $payload;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getLoadExtraData(): ?bool
    {
        return $this->loadExtraData;
    }
}
