<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Shared\Dto;

final class OwnerSearchRequest
{
    public function __construct(
        private readonly ?int $id = null,
        private readonly ?string $taxCode = null,
        private readonly ?string $businessTaxCode = null,
        private readonly ?string $vatNumber = null,
        private readonly ?string $surname = null,
        private readonly ?string $name = null,
        private readonly ?string $businessName = null,
        private readonly ?bool $toCorrectOnly = null
    ) {
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

        if ($this->taxCode !== null) {
            $payload['taxCode'] = $this->taxCode;
        }

        if ($this->businessTaxCode !== null) {
            $payload['businessTaxCode'] = $this->businessTaxCode;
        }

        if ($this->vatNumber !== null) {
            $payload['vatNumber'] = $this->vatNumber;
        }

        if ($this->surname !== null) {
            $payload['surname'] = $this->surname;
        }

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->businessName !== null) {
            $payload['businessName'] = $this->businessName;
        }

        if ($this->toCorrectOnly !== null) {
            $payload['toCorrectOnly'] = $this->toCorrectOnly;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function getToCorrectOnly(): ?bool
    {
        return $this->toCorrectOnly;
    }
}
