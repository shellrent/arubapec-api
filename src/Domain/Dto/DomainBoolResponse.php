<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\RestError;

final class DomainBoolResponse
{
    /**
     * @param RestError[] $errors
     */
    public function __construct(
        private readonly ?bool $data,
        private readonly string $version,
        private readonly array $errors
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (!isset($payload['version']) || !is_string($payload['version'])) {
            throw new UnexpectedResponseException('Missing response version field.');
        }

        $data = null;

        if (array_key_exists('data', $payload)) {
            $data = $payload['data'] === null ? null : (bool) $payload['data'];
        }

        $errors = [];

        if (isset($payload['errors']) && is_array($payload['errors'])) {
            foreach ($payload['errors'] as $error) {
                if (!is_array($error)) {
                    continue;
                }

                $errors[] = RestError::fromArray($error);
            }
        }

        return new self($data, $payload['version'], $errors);
    }

    public function getData(): ?bool
    {
        return $this->data;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return RestError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
