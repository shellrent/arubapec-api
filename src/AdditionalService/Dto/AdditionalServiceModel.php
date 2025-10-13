<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\AdditionalService\Dto;

use Carbon\CarbonImmutable;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\ContractData;
use Shellrent\Arubapec\Shared\Dto\RenewalData;

final class AdditionalServiceModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $type,
        private readonly string $status,
        private readonly ?string $value,
        private readonly ?CarbonImmutable $requestDate,
        private readonly ?CarbonImmutable $activationDate,
        private readonly ?CarbonImmutable $cancellationDate,
        private readonly ?CarbonImmutable $endDate,
        private readonly RenewalData $renewalData,
        private readonly ?ContractData $contractData
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        foreach (['id', 'type', 'status', 'renewalData'] as $field) {
            if (!isset($payload[$field])) {
                throw new UnexpectedResponseException(sprintf('Missing additional service field %s.', $field));
            }
        }

        if (!is_numeric($payload['id'])) {
            throw new UnexpectedResponseException('Invalid additional service id.');
        }

        if (!is_string($payload['type']) || !is_string($payload['status'])) {
            throw new UnexpectedResponseException('Invalid additional service data.');
        }

        $requestDate = self::parseDate($payload['requestDate'] ?? null, 'request date');
        $activationDate = self::parseDate($payload['activationDate'] ?? null, 'activation date');
        $cancellationDate = self::parseDate($payload['cancellationDate'] ?? null, 'cancellation date');
        $endDate = self::parseDate($payload['endDate'] ?? null, 'end date');

        $contractData = null;

        if (isset($payload['contractData']) && is_array($payload['contractData'])) {
            $contractData = ContractData::fromArray($payload['contractData']);
        }

        if (!is_array($payload['renewalData'])) {
            throw new UnexpectedResponseException('Invalid additional service renewal data.');
        }

        return new self(
            (int) $payload['id'],
            $payload['type'],
            $payload['status'],
            isset($payload['value']) ? (string) $payload['value'] : null,
            $requestDate,
            $activationDate,
            $cancellationDate,
            $endDate,
            RenewalData::fromArray($payload['renewalData']),
            $contractData
        );
    }

    private static function parseDate(mixed $value, string $context): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedResponseException(sprintf('Invalid %s value.', $context));
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable $throwable) {
            throw new UnexpectedResponseException(sprintf('Invalid %s value.', $context), 0, $throwable);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getRequestDate(): ?CarbonImmutable
    {
        return $this->requestDate;
    }

    public function getActivationDate(): ?CarbonImmutable
    {
        return $this->activationDate;
    }

    public function getCancellationDate(): ?CarbonImmutable
    {
        return $this->cancellationDate;
    }

    public function getEndDate(): ?CarbonImmutable
    {
        return $this->endDate;
    }

    public function getRenewalData(): RenewalData
    {
        return $this->renewalData;
    }

    public function getContractData(): ?ContractData
    {
        return $this->contractData;
    }
}
