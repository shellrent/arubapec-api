<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\AdditionalService\Dto;

final class AdditionalServiceChangeRenewalTypeRequest
{
    public function __construct(
        private readonly int $id,
        private readonly string $type
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
