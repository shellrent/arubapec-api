<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Owner\Dto;

final class OwnerContactData
{
    public function __construct(
        private readonly string $address,
        private readonly string $town,
        private readonly string $zipCode,
        private readonly string $district,
        private readonly string $email,
        private readonly string $telephoneNumber,
        private readonly ?int $country = null,
        private readonly ?string $mobile = null
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        $payload = [
            'address' => $this->address,
            'town' => $this->town,
            'zipCode' => $this->zipCode,
            'district' => $this->district,
            'email' => $this->email,
            'telephoneNumber' => $this->telephoneNumber,
        ];

        if ($this->country !== null) {
            $payload['country'] = $this->country;
        }

        if ($this->mobile !== null) {
            $payload['mobile'] = $this->mobile;
        }

        return $payload;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephoneNumber(): string
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
