<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Shared\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class ContactDataModel
{
    public function __construct(
        private readonly string $address,
        private readonly string $town,
        private readonly string $zipCode,
        private readonly string $district,
        private readonly ?int $country,
        private readonly string $email,
        private readonly string $telephoneNumber,
        private readonly ?string $mobile
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        foreach (['address', 'town', 'zipCode', 'district', 'email', 'telephoneNumber'] as $field) {
            if (!isset($payload[$field]) || !is_string($payload[$field])) {
                throw new UnexpectedResponseException(sprintf('Missing contact field %s.', $field));
            }
        }

        return new self(
            $payload['address'],
            $payload['town'],
            $payload['zipCode'],
            $payload['district'],
            isset($payload['country']) ? (int) $payload['country'] : null,
            $payload['email'],
            $payload['telephoneNumber'],
            isset($payload['mobile']) ? (string) $payload['mobile'] : null
        );
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getTown(): string
    {
        return $this->town;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getCountry(): ?int
    {
        return $this->country;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephoneNumber(): string
    {
        return $this->telephoneNumber;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }
}
