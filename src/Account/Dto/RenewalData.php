<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class RenewalData
{
    public function __construct(
        private readonly string $type,
        private readonly int $duration
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'duration' => $this->duration,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (!isset($payload['type']) || !is_string($payload['type'])) {
            throw new UnexpectedResponseException('Missing renewal type.');
        }

        if (!isset($payload['duration']) || !is_numeric($payload['duration'])) {
            throw new UnexpectedResponseException('Missing renewal duration.');
        }

        return new self($payload['type'], (int) $payload['duration']);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }
}
