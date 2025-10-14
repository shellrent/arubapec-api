<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Shared\Dto;

final class RestError
{
    public function __construct(
        private readonly ?string $code,
        private readonly ?string $description,
        private readonly ?ValidationError $validationError
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $validation = null;

        if (isset($payload['validationError']) && is_array($payload['validationError'])) {
            $validation = ValidationError::fromArray($payload['validationError']);
        }

        return new self(
            isset($payload['code']) ? (string) $payload['code'] : null,
            isset($payload['description']) ? (string) $payload['description'] : null,
            $validation
        );
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getValidationError(): ?ValidationError
    {
        return $this->validationError;
    }
}
