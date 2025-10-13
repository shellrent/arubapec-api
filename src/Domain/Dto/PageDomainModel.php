<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Domain\Dto;

use Shellrent\Arubapec\Account\Dto\PageableModel;
use Shellrent\Arubapec\Account\Dto\SortModel;

final class PageDomainModel
{
    /**
     * @param DomainModel[] $content
     * @param SortModel[] $sort
     */
    public function __construct(
        private readonly ?int $totalElements,
        private readonly ?int $totalPages,
        private readonly ?int $size,
        private readonly array $content,
        private readonly ?int $number,
        private readonly array $sort,
        private readonly ?int $numberOfElements,
        private readonly ?PageableModel $pageable,
        private readonly ?bool $first,
        private readonly ?bool $last,
        private readonly ?bool $empty
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $content = [];

        if (isset($payload['content']) && is_array($payload['content'])) {
            foreach ($payload['content'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $content[] = DomainModel::fromArray($item);
            }
        }

        $sort = [];

        if (isset($payload['sort']) && is_array($payload['sort'])) {
            foreach ($payload['sort'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $sort[] = SortModel::fromArray($item);
            }
        }

        $pageable = null;

        if (isset($payload['pageable']) && is_array($payload['pageable'])) {
            $pageable = PageableModel::fromArray($payload['pageable']);
        }

        return new self(
            isset($payload['totalElements']) ? (int) $payload['totalElements'] : null,
            isset($payload['totalPages']) ? (int) $payload['totalPages'] : null,
            isset($payload['size']) ? (int) $payload['size'] : null,
            $content,
            isset($payload['number']) ? (int) $payload['number'] : null,
            $sort,
            isset($payload['numberOfElements']) ? (int) $payload['numberOfElements'] : null,
            $pageable,
            isset($payload['first']) ? (bool) $payload['first'] : null,
            isset($payload['last']) ? (bool) $payload['last'] : null,
            isset($payload['empty']) ? (bool) $payload['empty'] : null
        );
    }

    public function getTotalElements(): ?int
    {
        return $this->totalElements;
    }

    public function getTotalPages(): ?int
    {
        return $this->totalPages;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @return DomainModel[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @return SortModel[]
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    public function getNumberOfElements(): ?int
    {
        return $this->numberOfElements;
    }

    public function getPageable(): ?PageableModel
    {
        return $this->pageable;
    }

    public function isFirst(): ?bool
    {
        return $this->first;
    }

    public function isLast(): ?bool
    {
        return $this->last;
    }

    public function isEmpty(): ?bool
    {
        return $this->empty;
    }
}
