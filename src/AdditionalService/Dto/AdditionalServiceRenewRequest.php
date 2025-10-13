<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\AdditionalService\Dto;

use Shellrent\Arubapec\Shared\Dto\ContractData;
use Shellrent\Arubapec\Shared\Dto\RenewalData;

final class AdditionalServiceRenewRequest
{
    public function __construct(
        private readonly int $id,
        private readonly RenewalData $renewalData,
        private readonly ?ContractData $contractData = null
    ) {
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        $payload = [
            'renewalData' => $this->renewalData->toArray(),
        ];

        if ($this->contractData !== null) {
            $payload['contractData'] = $this->contractData->toArray();
        }

        return $payload;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRenewalData(): RenewalData
    {
        return $this->renewalData;
    }

    public function getContractData(): ?ContractData
    {
        return $this->contractData;
    }
}
