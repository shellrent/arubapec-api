<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Country\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class CountryModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $name
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (!isset($payload['id']) || !is_int($payload['id'])) {
            throw new UnexpectedResponseException('Missing or invalid country id.');
        }

        if (!isset($payload['name']) || !is_string($payload['name'])) {
            throw new UnexpectedResponseException('Missing or invalid country name.');
        }

        return new self($payload['id'], $payload['name']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
