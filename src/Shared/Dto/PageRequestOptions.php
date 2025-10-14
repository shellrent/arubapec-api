<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Shared\Dto;

class PageRequestOptions
{
    /**
     * @param string[] $sort
     */
    public function __construct(
        private readonly ?int $page = null,
        private readonly ?int $size = null,
        private readonly array $sort = []
    ) {
    }

    /**
     * @return array<string, int|string|string[]>
     */
    public function toQuery(): array
    {
        $query = [];

        if ($this->page !== null) {
            $query['page'] = $this->page;
        }

        if ($this->size !== null) {
            $query['size'] = $this->size;
        }

        if ($this->sort !== []) {
            $query['sort'] = $this->sort;
        }

        return $query;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @return string[]
     */
    public function getSort(): array
    {
        return $this->sort;
    }
}
