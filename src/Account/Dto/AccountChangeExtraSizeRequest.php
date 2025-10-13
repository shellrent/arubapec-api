<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

final class AccountChangeExtraSizeRequest
{
    public function __construct(
        private readonly string $name,
        private readonly ?int $inboxExtra = null,
        private readonly ?int $archiveExtra = null,
        private readonly ?int $userPackageExtra = null,
        private readonly ?int $consExtra = null,
        private readonly ?string $intApp = null,
        private readonly ?string $pecMas = null,
        private readonly ?string $applyImmediately = null,
        private readonly ?ContractData $contractData = null
    ) {
    }

    /**
     * @return array<string, int|string|array<string, string>>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
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

        if ($this->applyImmediately !== null) {
            $payload['applyImmediately'] = $this->applyImmediately;
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

    public function getApplyImmediately(): ?string
    {
        return $this->applyImmediately;
    }

    public function getContractData(): ?ContractData
    {
        return $this->contractData;
    }
}
