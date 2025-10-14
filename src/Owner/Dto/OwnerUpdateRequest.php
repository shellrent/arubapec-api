<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Owner\Dto;

use Shellrent\Arubapec\Shared\Dto\OwnerId;

final class OwnerUpdateRequest
{
    public function __construct(
        private readonly OwnerId $ownerId,
        private readonly ?string $newTaxCode = null,
        private readonly ?string $newBusinessTaxCode = null,
        private readonly ?string $newVatNumber = null,
        private readonly ?string $businessName = null,
        private readonly ?string $name = null,
        private readonly ?string $surname = null,
        private readonly ?OwnerContactDataUpdate $contacts = null,
        private readonly ?OwnerContactDataUpdate $companyContacts = null
    ) {
    }

    /**
     * @return array<string, array<string, int|string>|string>
     */
    public function toArray(): array
    {
        $payload = [
            'ownerId' => $this->ownerId->toArray(),
        ];

        if ($this->newTaxCode !== null) {
            $payload['newTaxCode'] = $this->newTaxCode;
        }

        if ($this->newBusinessTaxCode !== null) {
            $payload['newBusinessTaxCode'] = $this->newBusinessTaxCode;
        }

        if ($this->newVatNumber !== null) {
            $payload['newVatNumber'] = $this->newVatNumber;
        }

        if ($this->businessName !== null) {
            $payload['businessName'] = $this->businessName;
        }

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->surname !== null) {
            $payload['surname'] = $this->surname;
        }

        if ($this->contacts !== null) {
            $payload['contacts'] = $this->contacts->toArray();
        }

        if ($this->companyContacts !== null) {
            $payload['companyContacts'] = $this->companyContacts->toArray();
        }

        return $payload;
    }

    public function getOwnerId(): OwnerId
    {
        return $this->ownerId;
    }

    public function getNewTaxCode(): ?string
    {
        return $this->newTaxCode;
    }

    public function getNewBusinessTaxCode(): ?string
    {
        return $this->newBusinessTaxCode;
    }

    public function getNewVatNumber(): ?string
    {
        return $this->newVatNumber;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getContacts(): ?OwnerContactDataUpdate
    {
        return $this->contacts;
    }

    public function getCompanyContacts(): ?OwnerContactDataUpdate
    {
        return $this->companyContacts;
    }
}
