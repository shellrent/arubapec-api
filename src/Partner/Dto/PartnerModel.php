<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Partner\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class PartnerModel
{
    public function __construct(
        private readonly int $remainingCredit,
        private readonly string $finalRemainingCredit
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (!isset($payload['remainingCredit']) || !is_int($payload['remainingCredit'])) {
            throw new UnexpectedResponseException('Missing or invalid remainingCredit.');
        }

        if (!isset($payload['finalRemainingCredit']) || !is_string($payload['finalRemainingCredit'])) {
            throw new UnexpectedResponseException('Missing or invalid finalRemainingCredit.');
        }

        return new self($payload['remainingCredit'], $payload['finalRemainingCredit']);
    }

    public function getRemainingCredit(): int
    {
        return $this->remainingCredit;
    }

    public function getFinalRemainingCredit(): string
    {
        return $this->finalRemainingCredit;
    }
}
