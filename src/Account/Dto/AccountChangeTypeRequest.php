<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Shared\Dto\ContractData;

final class AccountChangeTypeRequest
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly ?ContractData $contractData = null
    ) {
    }

    /**
     * @return array<string, string|array<string, string>>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'type' => $this->type,
        ];

        if ($this->contractData !== null) {
            $payload['contractData'] = $this->contractData->toArray();
        }

        return $payload;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getContractData(): ?ContractData
    {
        return $this->contractData;
    }
}
