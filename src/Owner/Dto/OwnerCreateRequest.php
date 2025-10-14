<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Owner\Dto;

final class OwnerCreateRequest
{
    public function __construct(
        private readonly string $userType,
        private readonly string $name,
        private readonly string $surname,
        private readonly string $taxCode,
        private readonly ?string $vatNumber = null,
        private readonly ?string $businessTaxCode = null,
        private readonly ?string $businessName = null,
        private readonly ?OwnerContactData $contacts = null,
        private readonly ?OwnerContactData $companyContacts = null
    ) {
    }

    /**
     * @return array<string, array<string, int|string>|string>
     */
    public function toArray(): array
    {
        $payload = [
            'userType' => $this->userType,
            'name' => $this->name,
            'surname' => $this->surname,
            'taxCode' => $this->taxCode,
        ];

        if ($this->vatNumber !== null) {
            $payload['vatNumber'] = $this->vatNumber;
        }

        if ($this->businessTaxCode !== null) {
            $payload['businessTaxCode'] = $this->businessTaxCode;
        }

        if ($this->businessName !== null) {
            $payload['businessName'] = $this->businessName;
        }

        if ($this->contacts !== null) {
            $payload['contacts'] = $this->contacts->toArray();
        }

        if ($this->companyContacts !== null) {
            $payload['companyContacts'] = $this->companyContacts->toArray();
        }

        return $payload;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getTaxCode(): string
    {
        return $this->taxCode;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function getBusinessTaxCode(): ?string
    {
        return $this->businessTaxCode;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function getContacts(): ?OwnerContactData
    {
        return $this->contacts;
    }

    public function getCompanyContacts(): ?OwnerContactData
    {
        return $this->companyContacts;
    }
}
