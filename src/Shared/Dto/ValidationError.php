<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Shared\Dto;

final class ValidationError
{
    public function __construct(
        private readonly ?string $objectName,
        private readonly ?string $field,
        private readonly ?string $code,
        private readonly mixed $rejectedValue,
        private readonly ?string $defaultMessage
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            isset($payload['objectName']) ? (string) $payload['objectName'] : null,
            isset($payload['field']) ? (string) $payload['field'] : null,
            isset($payload['code']) ? (string) $payload['code'] : null,
            $payload['rejectedValue'] ?? null,
            isset($payload['defaultMessage']) ? (string) $payload['defaultMessage'] : null
        );
    }

    public function getObjectName(): ?string
    {
        return $this->objectName;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getRejectedValue(): mixed
    {
        return $this->rejectedValue;
    }

    public function getDefaultMessage(): ?string
    {
        return $this->defaultMessage;
    }
}
