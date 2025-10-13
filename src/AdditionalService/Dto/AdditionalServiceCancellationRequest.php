<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\AdditionalService\Dto;

use Carbon\CarbonImmutable;

final class AdditionalServiceCancellationRequest
{
    public function __construct(
        private readonly int $id,
        private readonly ?string $type = null,
        private readonly ?CarbonImmutable $cancellationDate = null
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->type !== null) {
            $payload['type'] = $this->type;
        }

        if ($this->cancellationDate !== null) {
            $payload['cancellationDate'] = $this->cancellationDate->toIso8601String();
        }

        return $payload;
    }

    public function getId(): int
    {
        return $this->id;
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
