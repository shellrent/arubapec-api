<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Auth\Dto;

use Carbon\CarbonImmutable;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\RestError;

final class TokenResponse
{
    /**
     * @param RestError[] $errors
     */
    public function __construct(
        private readonly ?TokenModel $data,
        private readonly ?CarbonImmutable $datetime,
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
            $data = TokenModel::fromArray($payload['data']);
        }

        $datetime = null;

        if (isset($payload['datetime']) && is_string($payload['datetime']) && $payload['datetime'] !== '') {
            try {
                $datetime = CarbonImmutable::parse($payload['datetime']);
            } catch (\Throwable $throwable) {
                throw new UnexpectedResponseException('Invalid datetime format in response.', 0, $throwable);
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

        return new self($data, $datetime, $payload['version'], $errors);
    }

    public function getData(): ?TokenModel
    {
        return $this->data;
    }

    public function getDatetime(): ?CarbonImmutable
    {
        return $this->datetime;
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

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }
}
