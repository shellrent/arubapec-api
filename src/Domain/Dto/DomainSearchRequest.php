<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

use Shellrent\Arubapec\Account\Dto\Interval;
use Shellrent\Arubapec\Shared\Dto\OwnerSearchRequest;

final class DomainSearchRequest
{
    public function __construct(
        private readonly ?OwnerSearchRequest $owner = null,
        private readonly ?Interval $activationDate = null,
        private readonly ?string $type = null,
        private readonly ?string $status = null
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->owner !== null) {
            $payload['owner'] = $this->owner->toArray();
        }

        if ($this->activationDate !== null) {
            $payload['activationDate'] = $this->activationDate->toArray();
        }

        if ($this->type !== null) {
            $payload['type'] = $this->type;
        }

        if ($this->status !== null) {
            $payload['status'] = $this->status;
        }

        return $payload;
    }

    public function getOwner(): ?OwnerSearchRequest
    {
        return $this->owner;
    }

    public function getActivationDate(): ?Interval
    {
        return $this->activationDate;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }
}
