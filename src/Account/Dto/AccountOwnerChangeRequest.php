<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

final class AccountOwnerChangeRequest
{
    public function __construct(
        private readonly string $name,
        private readonly int $newOwnerId,
        private readonly string $recoveryEmail,
        private readonly string $mobile
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'newOwnerId' => $this->newOwnerId,
            'recoveryEmail' => $this->recoveryEmail,
            'mobile' => $this->mobile,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNewOwnerId(): int
    {
        return $this->newOwnerId;
    }

    public function getrecoveryEmail(): string
    {
        return $this->recoveryEmail;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }
}
