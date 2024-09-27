<?php

namespace App\Features\Properties\Property\DTO\Repository;

final readonly class PropertyFilterDTO
{
    /** @param PropertyFilterValueDTO[] $values */
    public function __construct(
        public string $_id,
        public int $count,
        public array $values,
    ) {
    }
}