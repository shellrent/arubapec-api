<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

use Carbon\CarbonImmutable;
use Shellrent\Arubapec\Shared\Dto\OwnerModel;
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceModel;
use Shellrent\Arubapec\Exception\UnexpectedResponseException;
use Shellrent\Arubapec\Shared\Dto\ContractData;

final class DomainTypologyModel
{
    /**
     * @param AdditionalServiceModel[] $additionalServices
     */
    public function __construct(
        private readonly string $typology,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
		if (!array_key_exists('typology', $payload)) {
                throw new UnexpectedResponseException(sprintf('Missing domain field typology'));
		}

        if (!is_string($payload['typology'])) {
            throw new UnexpectedResponseException('Invalid domain typology or status.');
        }
		

        return new self(
            $payload['typology'],
        );
    }

    public function getTypology(): string
    {
        return $this->typology;
    }
}
