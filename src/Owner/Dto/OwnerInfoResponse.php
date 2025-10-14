<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Owner\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\OwnerModel;
use Shellrent\Arubapec\Shared\Dto\RestError;

final class OwnerInfoResponse
{
    /**
     * @param RestError[] $errors
     */
    public function __construct(
        private readonly ?OwnerModel $data,
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

        if (isset($payload['data']) && is_array($payload['data'])) {
            $data = OwnerModel::fromArray($payload['data']);
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

    public function getData(): ?OwnerModel
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
