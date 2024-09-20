<?php

namespace App\Features\Properties\Property\DTO\Repository;

final readonly class PropertyFilterValueDTO
{
    public function __construct(
        public string $unitCode,
        public string $valueCode,
        public string $valueName,
        public string $productCode,

    ) {
    }
}