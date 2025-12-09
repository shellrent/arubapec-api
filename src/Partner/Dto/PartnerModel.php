<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Partner\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class PartnerModel
{
    public function __construct(
        private readonly float $remainingCredit,
        private readonly ?string $finalRemainingCredit = null
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (!isset($payload['remainingCredit']) || !is_string($payload['remainingCredit'])) {
            throw new UnexpectedResponseException('Missing or invalid remainingCredit.');
        }

        return new self((float)$payload['remainingCredit'], $payload['finalRemainingCredit'] ?? null);
    }

    public function getRemainingCredit(): float
    {
        return $this->remainingCredit;
    }

    public function getFinalRemainingCredit(): string
    {
        return $this->finalRemainingCredit;
    }
}
