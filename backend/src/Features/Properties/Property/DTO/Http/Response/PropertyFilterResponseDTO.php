<?php

namespace App\Features\Properties\Property\DTO\Http\Response;

final readonly class PropertyFilterResponseDTO
{
    public function __construct(
        /** @var PropertyFilterItemResponseDTO[] $filters */
        public array $filters = [],
        public int $count = 0,
    ) {
    }
}