<?php

namespace App\Features\Pagination\DTO;

final readonly class PaginationDTO
{
    public function __construct(
        public int $page = 1,
        public int $limit = 10,
    ) {
    }

    public function getLimit(): int
    {
        return $this->page * $this->limit;
    }

    public function getSkip(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}