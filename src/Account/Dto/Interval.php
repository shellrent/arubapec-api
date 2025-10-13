<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Carbon\CarbonImmutable;

final class Interval
{
    public function __construct(
        private readonly ?CarbonImmutable $from = null,
        private readonly ?CarbonImmutable $to = null
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->from !== null) {
            $payload['from'] = $this->from->toIso8601String();
        }

        if ($this->to !== null) {
            $payload['to'] = $this->to->toIso8601String();
        }

        return $payload;
    }

    public function getFrom(): ?CarbonImmutable
    {
        return $this->from;
    }

    public function getTo(): ?CarbonImmutable
    {
        return $this->to;
    }
}
