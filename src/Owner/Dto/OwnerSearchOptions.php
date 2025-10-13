<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Owner\Dto;

use Shellrent\Arubapec\Shared\Dto\PageRequestOptions;

final class OwnerSearchOptions extends PageRequestOptions
{
    /**
     * @param string[] $sort
     */
    public function __construct(?int $page = null, ?int $size = null, array $sort = [])
    {
        parent::__construct($page, $size, $sort);
    }
}
