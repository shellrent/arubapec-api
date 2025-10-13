<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class OwnerModel
{
    public function __construct(
        private readonly string $userType,
        private readonly string $name,
        private readonly string $surname,
        private readonly string $taxCode,
        private readonly ?string $vatNumber,
        private readonly ?string $businessTaxCode,
        private readonly ?string $businessName,
        private readonly ?ContactDataModel $contacts,
        private readonly ?CompanyContactDataModel $companyContacts,
        private readonly ?int $id
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        foreach (['userType', 'name', 'surname', 'taxCode'] as $field) {
            if (!isset($payload[$field]) || !is_string($payload[$field])) {
                throw new UnexpectedResponseException(sprintf('Missing owner field %s.', $field));
            }
        }

        $contacts = null;

        if (isset($payload['contacts']) && is_array($payload['contacts'])) {
            $contacts = ContactDataModel::fromArray($payload['contacts']);
        }

        $companyContacts = null;

        if (isset($payload['companyContacts']) && is_array($payload['companyContacts'])) {
            $companyContacts = CompanyContactDataModel::fromArray($payload['companyContacts']);
        }

        return new self(
            $payload['userType'],
            $payload['name'],
            $payload['surname'],
            $payload['taxCode'],
            isset($payload['vatNumber']) ? (string) $payload['vatNumber'] : null,
            isset($payload['businessTaxCode']) ? (string) $payload['businessTaxCode'] : null,
            isset($payload['businessName']) ? (string) $payload['businessName'] : null,
            $contacts,
            $companyContacts,
            isset($payload['id']) ? (int) $payload['id'] : null
        );
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

    public function getContacts(): ?ContactDataModel
    {
        return $this->contacts;
    }

    public function getCompanyContacts(): ?CompanyContactDataModel
    {
        return $this->companyContacts;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
