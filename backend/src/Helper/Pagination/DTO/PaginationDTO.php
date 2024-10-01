<?php

namespace App\Helper\Pagination\DTO;

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

    public static function sliceArray(array $data, int $page, int $limit): array
    {
        return array_slice($data, ($limit * ($page - 1)), $limit);;
    }
}