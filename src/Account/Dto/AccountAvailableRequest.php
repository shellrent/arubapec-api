<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

final class AccountAvailableRequest
{
    public function __construct(
        private readonly string $name,
        private readonly string $domain,
        private readonly ?string $taxCode = null,
        private readonly ?string $vatNumber = null
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'domain' => $this->domain,
        ];

        if ($this->taxCode !== null) {
            $payload['taxCode'] = $this->taxCode;
        }

        if ($this->vatNumber !== null) {
            $payload['vatNumber'] = $this->vatNumber;
        }

        return $payload;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getTaxCode(): ?string
    {
        return $this->taxCode;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }
}
