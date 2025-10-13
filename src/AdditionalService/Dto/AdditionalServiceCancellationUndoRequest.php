<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\AdditionalService\Dto;

final class AdditionalServiceCancellationUndoRequest
{
    public function __construct(private readonly int $id)
    {
    }

    /**
     * @return array{}
     */
    public function toArray(): array
    {
        return [];
    }

    public function getId(): int
    {
        return $this->id;
    }
}
