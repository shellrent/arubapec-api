<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Carbon\CarbonImmutable;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceModel;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\ContractData;
use Shellrent\Arubapec\Shared\Dto\OwnerModel;
use Shellrent\Arubapec\Shared\Dto\RenewalData;

final class AccountModel
{
    /**
     * @param InfoQuotaModel[] $quotas
     * @param AdditionalServiceModel[] $additionalServices
     */
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly string $status,
        private readonly array $quotas,
        private readonly OwnerModel $owner,
        private readonly CarbonImmutable $requestDate,
        private readonly CarbonImmutable $certificationDate,
        private readonly ?CarbonImmutable $cancellationDate,
        private readonly ?CarbonImmutable $suspensionDate,
        private readonly CarbonImmutable $endDate,
        private readonly RenewalData $renewalData,
        private readonly ?ContractData $contractData,
        private readonly array $additionalServices
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        foreach (['name', 'type', 'status', 'owner', 'quotas', 'requestDate', 'certificationDate', 'endDate', 'renewalData'] as $field) {
            if (!isset($payload[$field])) {
                throw new UnexpectedResponseException(sprintf('Missing account field %s.', $field));
            }
        }

        if (!is_string($payload['name']) || !is_string($payload['type']) || !is_string($payload['status'])) {
            throw new UnexpectedResponseException('Invalid account string fields.');
        }

        if (!is_array($payload['owner']) || !is_array($payload['quotas']) || !is_array($payload['renewalData'])) {
            throw new UnexpectedResponseException('Invalid account payload types.');
        }

        $quotas = [];

        foreach ($payload['quotas'] as $quota) {
            if (!is_array($quota)) {
                continue;
            }

            $quotas[] = InfoQuotaModel::fromArray($quota);
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

        $contractData = null;

        if (isset($payload['contractData']) && is_array($payload['contractData'])) {
            $contractData = ContractData::fromArray($payload['contractData']);
        }

        return new self(
            $payload['name'],
            $payload['type'],
            $payload['status'],
            $quotas,
            OwnerModel::fromArray($payload['owner']),
            self::parseDate($payload['requestDate'], 'request date'),
            self::parseDate($payload['certificationDate'], 'certification date'),
            self::parseOptionalDate($payload['cancellationDate'] ?? null, 'cancellation date'),
            self::parseOptionalDate($payload['suspensionDate'] ?? null, 'suspension date'),
            self::parseDate($payload['endDate'], 'end date'),
            RenewalData::fromArray($payload['renewalData']),
            $contractData,
            $additionalServices
        );
    }

    private static function parseDate(mixed $value, string $context): CarbonImmutable
    {
        $date = self::parseOptionalDate($value, $context);

        if ($date === null) {
            throw new UnexpectedResponseException(sprintf('Missing %s.', $context));
        }

        return $date;
    }

    private static function parseOptionalDate(mixed $value, string $context): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedResponseException(sprintf('Invalid %s.', $context));
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable $throwable) {
            throw new UnexpectedResponseException(sprintf('Invalid %s.', $context), 0, $throwable);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return InfoQuotaModel[]
     */
    public function getQuotas(): array
    {
        return $this->quotas;
    }

    public function getOwner(): OwnerModel
    {
        return $this->owner;
    }

    public function getRequestDate(): CarbonImmutable
    {
        return $this->requestDate;
    }

    public function getCertificationDate(): CarbonImmutable
    {
        return $this->certificationDate;
    }

    public function getCancellationDate(): ?CarbonImmutable
    {
        return $this->cancellationDate;
    }

    public function getSuspensionDate(): ?CarbonImmutable
    {
        return $this->suspensionDate;
    }

    public function getEndDate(): CarbonImmutable
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

    /**
     * @return AdditionalServiceModel[]
     */
    public function getAdditionalServices(): array
    {
        return $this->additionalServices;
    }
}
