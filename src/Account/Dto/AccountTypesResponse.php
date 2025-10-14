<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\RestError;

final class AccountTypesResponse
{
    /**
     * @param AccountTypeModel[] $data
     * @param RestError[] $errors
     */
    public function __construct(
        private readonly array $data,
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

        $data = [];

        if (isset($payload['data']) && is_array($payload['data'])) {
            foreach ($payload['data'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $data[] = AccountTypeModel::fromArray($item);
            }
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

    /**
     * @return AccountTypeModel[]
     */
    public function getData(): array
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
