<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

final class DomainCertifyRequest
{
    public function __construct(
        private readonly string $fullName,
        private readonly string $typology,
        private readonly int $ownerId,
        private readonly ?string $cigOda = null,
        private readonly ?string $sdiCode = null
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        $payload = [
            'fullName' => $this->fullName,
            'typology' => $this->typology,
            'ownerId' => $this->ownerId,
        ];

        if ($this->cigOda !== null) {
            $payload['cigOda'] = $this->cigOda;
        }

        if ($this->sdiCode !== null) {
            $payload['sdiCode'] = $this->sdiCode;
        }

        return $payload;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getTypology(): string
    {
        return $this->typology;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getCigOda(): ?string
    {
        return $this->cigOda;
    }

    public function getSdiCode(): ?string
    {
        return $this->sdiCode;
    }
}
