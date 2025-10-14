<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Shared\Dto;

use Carbon\CarbonImmutable;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class RestErrorResponse
{
    /**
     * @param RestError[] $errors
     */
    public function __construct(
        private readonly ?string $data,
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

        return new self(
            isset($payload['data']) ? (string) $payload['data'] : null,
            $datetime,
            $payload['version'],
            $errors
        );
    }

    public function getData(): ?string
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

    public function getFirstErrorDescription(): ?string
    {
        foreach ($this->errors as $error) {
            if ($error->getDescription() !== null) {
                return $error->getDescription();
            }
        }

        return null;
    }
}
