<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

final class PageableModel
{
    /**
     * @param SortModel[] $sort
     */
    public function __construct(
        private readonly ?int $offset,
        private readonly array $sort,
        private readonly ?bool $unpaged,
        private readonly ?bool $paged,
        private readonly ?int $pageNumber,
        private readonly ?int $pageSize
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $sort = [];

        if (isset($payload['sort']) && is_array($payload['sort'])) {
            foreach ($payload['sort'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $sort[] = SortModel::fromArray($item);
            }
        }

        return new self(
            isset($payload['offset']) ? (int) $payload['offset'] : null,
            $sort,
            isset($payload['unpaged']) ? (bool) $payload['unpaged'] : null,
            isset($payload['paged']) ? (bool) $payload['paged'] : null,
            isset($payload['pageNumber']) ? (int) $payload['pageNumber'] : null,
            isset($payload['pageSize']) ? (int) $payload['pageSize'] : null
        );
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return SortModel[]
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    public function isUnpaged(): ?bool
    {
        return $this->unpaged;
    }

    public function isPaged(): ?bool
    {
        return $this->paged;
    }

    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }
}
