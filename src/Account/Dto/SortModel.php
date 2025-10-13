<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class SortModel
{
    public function __construct(
        private readonly ?string $direction,
        private readonly ?string $nullHandling,
        private readonly ?bool $ascending,
        private readonly ?string $property,
        private readonly ?bool $ignoreCase
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (isset($payload['direction']) && !is_string($payload['direction'])) {
            throw new UnexpectedResponseException('Invalid sort direction.');
        }

        if (isset($payload['nullHandling']) && !is_string($payload['nullHandling'])) {
            throw new UnexpectedResponseException('Invalid sort null handling.');
        }

        if (isset($payload['property']) && !is_string($payload['property'])) {
            throw new UnexpectedResponseException('Invalid sort property.');
        }

        return new self(
            isset($payload['direction']) ? (string) $payload['direction'] : null,
            isset($payload['nullHandling']) ? (string) $payload['nullHandling'] : null,
            isset($payload['ascending']) ? (bool) $payload['ascending'] : null,
            isset($payload['property']) ? (string) $payload['property'] : null,
            isset($payload['ignoreCase']) ? (bool) $payload['ignoreCase'] : null
        );
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function getNullHandling(): ?string
    {
        return $this->nullHandling;
    }

    public function isAscending(): ?bool
    {
        return $this->ascending;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function getIgnoreCase(): ?bool
    {
        return $this->ignoreCase;
    }
}
