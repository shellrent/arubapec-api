<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class InfoQuotaModel
{
    public function __construct(
        private readonly string $type,
        private readonly int $base,
        private readonly ?int $extra,
        private readonly ?int $massive,
        private readonly ?int $used,
        private readonly ?int $available,
        private readonly ?string $description
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (!isset($payload['type']) || !is_string($payload['type'])) {
            throw new UnexpectedResponseException('Missing quota type.');
        }

        if (!isset($payload['base']) || !is_numeric($payload['base'])) {
            throw new UnexpectedResponseException('Missing quota base.');
        }

        return new self(
            $payload['type'],
            (int) $payload['base'],
            isset($payload['extra']) ? (int) $payload['extra'] : null,
            isset($payload['massive']) ? (int) $payload['massive'] : null,
            isset($payload['used']) ? (int) $payload['used'] : null,
            isset($payload['available']) ? (int) $payload['available'] : null,
            isset($payload['description']) ? (string) $payload['description'] : null
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getBase(): int
    {
        return $this->base;
    }

    public function getExtra(): ?int
    {
        return $this->extra;
    }

    public function getMassive(): ?int
    {
        return $this->massive;
    }

    public function getUsed(): ?int
    {
        return $this->used;
    }

    public function getAvailable(): ?int
    {
        return $this->available;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
