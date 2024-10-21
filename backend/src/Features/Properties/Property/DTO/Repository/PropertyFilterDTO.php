<?php

namespace App\Features\Properties\Property\DTO\Repository;

final readonly class PropertyFilterDTO
{
    public function __construct(
        public string $featureCode,
        public string $valueCode,
        public string $unitCode,
        /** @var string[] $productCodes */
        public array $productCodes,
    ) {
    }
}