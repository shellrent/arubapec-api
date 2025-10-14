<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Carbon\CarbonImmutable;

final class AccountCancellationRequest
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $type = null,
        private readonly ?CarbonImmutable $cancellationDate = null
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
        ];

        if ($this->type !== null) {
            $payload['type'] = $this->type;
        }

        if ($this->cancellationDate !== null) {
            $payload['cancellationDate'] = $this->cancellationDate->toIso8601String();
        }

        return $payload;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getCancellationDate(): ?CarbonImmutable
    {
        return $this->cancellationDate;
    }
}
