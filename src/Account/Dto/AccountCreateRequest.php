<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Shared\Dto\ContractData;
use Shellrent\Arubapec\Shared\Dto\OwnerId;
use Shellrent\Arubapec\Shared\Dto\RenewalData;

final class AccountCreateRequest
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly string $recoveryEmail,
        private readonly OwnerId $ownerId,
        private readonly RenewalData $renewalData,
        private readonly ?int $inboxExtra = null,
        private readonly ?int $archiveExtra = null,
        private readonly ?int $userPackageExtra = null,
        private readonly ?int $consExtra = null,
        private readonly ?string $intApp = null,
        private readonly ?string $pecMas = null,
        private readonly ?ContractData $contractData = null
    ) {
    }

    /**
     * @return array<string, int|string|array<string, mixed>>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'type' => $this->type,
            'recoveryEmail' => $this->recoveryEmail,
            'ownerId' => $this->ownerId->toArray(),
            'renewalData' => $this->renewalData->toArray(),
        ];

        if ($this->inboxExtra !== null) {
            $payload['inboxExtra'] = $this->inboxExtra;
        }

        if ($this->archiveExtra !== null) {
            $payload['archiveExtra'] = $this->archiveExtra;
        }

        if ($this->userPackageExtra !== null) {
            $payload['userPackageExtra'] = $this->userPackageExtra;
        }

        if ($this->consExtra !== null) {
            $payload['consExtra'] = $this->consExtra;
        }

        if ($this->intApp !== null) {
            $payload['intApp'] = $this->intApp;
        }

        if ($this->pecMas !== null) {
            $payload['pecMas'] = $this->pecMas;
        }

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

    public function getRecoveryEmail()
    {
        return $this->recoveryEmail;
    }
    public function getOwnerId(): OwnerId
    {
        return $this->ownerId;
    }

    public function getRenewalData(): RenewalData
    {
        return $this->renewalData;
    }

    public function getInboxExtra(): ?int
    {
        return $this->inboxExtra;
    }

    public function getArchiveExtra(): ?int
    {
        return $this->archiveExtra;
    }

    public function getUserPackageExtra(): ?int
    {
        return $this->userPackageExtra;
    }

    public function getConsExtra(): ?int
    {
        return $this->consExtra;
    }

    public function getIntApp(): ?string
    {
        return $this->intApp;
    }

    public function getPecMas(): ?string
    {
        return $this->pecMas;
    }

    public function getContractData(): ?ContractData
    {
        return $this->contractData;
    }
}
