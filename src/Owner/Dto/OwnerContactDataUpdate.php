<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Owner\Dto;

final class OwnerContactDataUpdate
{
    public function __construct(
        private readonly ?string $address = null,
        private readonly ?string $town = null,
        private readonly ?string $zipCode = null,
        private readonly ?string $district = null,
        private readonly ?string $email = null,
        private readonly ?string $telephoneNumber = null,
        private readonly ?int $country = null,
        private readonly ?string $mobile = null
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->address !== null) {
            $payload['address'] = $this->address;
        }

        if ($this->town !== null) {
            $payload['town'] = $this->town;
        }

        if ($this->zipCode !== null) {
            $payload['zipCode'] = $this->zipCode;
        }

        if ($this->district !== null) {
            $payload['district'] = $this->district;
        }

        if ($this->email !== null) {
            $payload['email'] = $this->email;
        }

        if ($this->telephoneNumber !== null) {
            $payload['telephoneNumber'] = $this->telephoneNumber;
        }

        if ($this->country !== null) {
            $payload['country'] = $this->country;
        }

        if ($this->mobile !== null) {
            $payload['mobile'] = $this->mobile;
        }

        return $payload;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTelephoneNumber(): ?string
    {
        return $this->telephoneNumber;
    }

    public function getCountry(): ?int
    {
        return $this->country;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }
}
