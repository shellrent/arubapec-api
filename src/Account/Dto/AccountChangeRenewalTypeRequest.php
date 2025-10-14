<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

final class AccountChangeRenewalTypeRequest
{
    public function __construct(
        private readonly string $name,
        private readonly string $type
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
