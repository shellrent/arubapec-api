<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\AdditionalService\Dto;

use Shellrent\Arubapec\Shared\Dto\ContractData;
use Shellrent\Arubapec\Shared\Dto\RenewalData;

final class AdditionalServiceCreateRequest
{
    public function __construct(
        private readonly string $account,
        private readonly string $type,
        private readonly RenewalData $renewalData,
        private readonly ?string $value = null,
        private readonly ?ContractData $contractData = null
    ) {
    }

    /**
     * @return array<string, array<string, mixed>|string>
     */
    public function toArray(): array
    {
        $payload = [
            'account' => $this->account,
            'type' => $this->type,
            'renewalData' => $this->renewalData->toArray(),
        ];

        if ($this->value !== null) {
            $payload['value'] = $this->value;
        }

        if ($this->contractData !== null) {
            $payload['contractData'] = $this->contractData->toArray();
        }

        return $payload;
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRenewalData(): RenewalData
    {
        return $this->renewalData;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getContractData(): ?ContractData
    {
        return $this->contractData;
    }
}
