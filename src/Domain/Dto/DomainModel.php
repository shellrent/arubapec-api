<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

use Carbon\CarbonImmutable;
use Shellrent\Arubapec\Shared\Dto\OwnerModel;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceModel;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\ContractData;

final class DomainModel
{
    /**
     * @param AdditionalServiceModel[] $additionalServices
     */
    public function __construct(
        private readonly string $fullName,
        private readonly string $typology,
        private readonly string $status,
        private readonly CarbonImmutable $requestDate,
        private readonly ?CarbonImmutable $certificationDate,
        private readonly ?CarbonImmutable $cancellationDate,
        private readonly ?CarbonImmutable $endDate,
        private readonly OwnerModel $owner,
        private readonly ?ContractData $contractData,
        private readonly array $additionalServices
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        foreach (['fullName', 'typology', 'status', 'requestDate', 'owner'] as $field) {
            if (!array_key_exists($field, $payload)) {
                throw new UnexpectedResponseException(sprintf('Missing domain field %s.', $field));
            }
        }

        if (!is_string($payload['fullName']) || $payload['fullName'] === '') {
            throw new UnexpectedResponseException('Invalid domain full name.');
        }

        if (!is_string($payload['typology']) || !is_string($payload['status'])) {
            throw new UnexpectedResponseException('Invalid domain typology or status.');
        }

        if (!is_string($payload['requestDate']) || $payload['requestDate'] === '') {
            throw new UnexpectedResponseException('Invalid domain request date.');
        }

        if (!is_array($payload['owner'])) {
            throw new UnexpectedResponseException('Invalid domain owner payload.');
        }

        $requestDate = self::parseDate($payload['requestDate'], 'request date');

        $cancellationDate = null;
        $certificationDate = null;
        $endDate = null;

        if (array_key_exists('cancellationDate', $payload)) {
            $cancellationDate = self::parseOptionalDate($payload['cancellationDate'], 'cancellation date');
        }

        if (array_key_exists('certificationDate', $payload)) {
            $certificationDate = self::parseOptionalDate($payload['certificationDate'], 'certification date');
        }

        if (array_key_exists('endDate', $payload)) {
            $endDate = self::parseOptionalDate($payload['endDate'], 'end date');
        }

        $contractData = null;

        if (isset($payload['contractData']) && is_array($payload['contractData'])) {
            $contractData = ContractData::fromArray($payload['contractData']);
        }

        $additionalServices = [];

        if (isset($payload['additionalServices']) && is_array($payload['additionalServices'])) {
            foreach ($payload['additionalServices'] as $service) {
                if (!is_array($service)) {
                    continue;
                }

                $additionalServices[] = AdditionalServiceModel::fromArray($service);
            }
        }

        return new self(
            $payload['fullName'],
            $payload['typology'],
            $payload['status'],
            $requestDate,
            $certificationDate,
            $cancellationDate,
            $endDate,
            OwnerModel::fromArray($payload['owner']),
            $contractData,
            $additionalServices
        );
    }

    private static function parseDate(string $value, string $context): CarbonImmutable
    {
        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable $throwable) {
            throw new UnexpectedResponseException(sprintf('Invalid domain %s value.', $context), 0, $throwable);
        }
    }

    private static function parseOptionalDate(mixed $value, string $context): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedResponseException(sprintf('Invalid domain %s value.', $context));
        }

        return self::parseDate($value, $context);
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getTypology(): string
    {
        return $this->typology;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRequestDate(): CarbonImmutable
    {
        return $this->requestDate;
    }

    public function getCertificationDate(): ?CarbonImmutable
    {
        return $this->certificationDate;
    }

    public function getCancellationDate(): ?CarbonImmutable
    {
        return $this->cancellationDate;
    }

    public function getEndDate(): ?CarbonImmutable
    {
        return $this->endDate;
    }

    public function getOwner(): OwnerModel
    {
        return $this->owner;
    }

    public function getContractData(): ?ContractData
    {
        return $this->contractData;
    }

    /**
     * @return AdditionalServiceModel[]
     */
    public function getAdditionalServices(): array
    {
        return $this->additionalServices;
    }
}
