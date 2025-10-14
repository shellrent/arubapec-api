<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Shared\Dto;

final class OwnerId
{
    public function __construct(
        private readonly ?int $id = null,
        private readonly ?string $taxCode = null,
        private readonly ?string $businessTaxCode = null,
        private readonly ?string $vatNumber = null
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->id !== null) {
            $payload['id'] = $this->id;
        }

        if ($this->taxCode !== null) {
            $payload['taxCode'] = $this->taxCode;
        }

        if ($this->businessTaxCode !== null) {
            $payload['businessTaxCode'] = $this->businessTaxCode;
        }

        if ($this->vatNumber !== null) {
            $payload['vatNumber'] = $this->vatNumber;
        }

        return $payload;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxCode(): ?string
    {
        return $this->taxCode;
    }

    public function getBusinessTaxCode(): ?string
    {
        return $this->businessTaxCode;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }
}
