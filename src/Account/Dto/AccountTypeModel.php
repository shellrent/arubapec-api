<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class AccountTypeModel
{
    public function __construct(
        private readonly string $type,
        private readonly string $description,
        private readonly int $inboxBase,
        private readonly ?int $inboxMaxExtra,
        private readonly ?int $archiveBase,
        private readonly ?int $archiveMaxExtra
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        foreach (['type', 'description', 'inboxBase'] as $field) {
            if (!isset($payload[$field])) {
                throw new UnexpectedResponseException(sprintf('Missing account type field %s.', $field));
            }
        }

        if (!is_string($payload['type']) || !is_string($payload['description']) || !is_numeric($payload['inboxBase'])) {
            throw new UnexpectedResponseException('Invalid account type payload.');
        }

        return new self(
            $payload['type'],
            $payload['description'],
            (int) $payload['inboxBase'],
            isset($payload['inboxMaxExtra']) ? (int) $payload['inboxMaxExtra'] : null,
            isset($payload['archiveBase']) ? (int) $payload['archiveBase'] : null,
            isset($payload['archiveMaxExtra']) ? (int) $payload['archiveMaxExtra'] : null
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getInboxBase(): int
    {
        return $this->inboxBase;
    }

    public function getInboxMaxExtra(): ?int
    {
        return $this->inboxMaxExtra;
    }

    public function getArchiveBase(): ?int
    {
        return $this->archiveBase;
    }

    public function getArchiveMaxExtra(): ?int
    {
        return $this->archiveMaxExtra;
    }
}
