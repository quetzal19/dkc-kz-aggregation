<?php

namespace App\Features\Priority\Filter;

use App\Helper\Pagination\DTO\PaginationDTO;

final readonly class PriorityFilter
{
    public function __construct(
        public ?PaginationDTO $paginationDTO = null,
        public ?int $actionPriority = null,
        public ?int $priority = null,
        public ?string $entity = null,
        public ?string $action = null,
    ) {
    }
}