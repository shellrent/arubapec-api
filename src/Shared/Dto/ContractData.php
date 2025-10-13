<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Shared\Dto;

final class ContractData
{
    public function __construct(
        private readonly ?string $sdiCode = null,
        private readonly ?string $cigOda = null
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->sdiCode !== null) {
            $payload['sdiCode'] = $this->sdiCode;
        }

        if ($this->cigOda !== null) {
            $payload['cigOda'] = $this->cigOda;
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            isset($payload['sdiCode']) ? (string) $payload['sdiCode'] : null,
            isset($payload['cigOda']) ? (string) $payload['cigOda'] : null
        );
    }

    public function getSdiCode(): ?string
    {
        return $this->sdiCode;
    }

    public function getCigOda(): ?string
    {
        return $this->cigOda;
    }
}
