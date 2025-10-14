<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

final class AccountInfoRequest
{
    public function __construct(
        private readonly string $name,
        private readonly ?bool $loadExtraData = null
    ) {
    }

    /**
     * @return array<string, bool|string>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
        ];

        if ($this->loadExtraData !== null) {
            $payload['loadExtraData'] = $this->loadExtraData;
        }

        return $payload;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLoadExtraData(): ?bool
    {
        return $this->loadExtraData;
    }
}
