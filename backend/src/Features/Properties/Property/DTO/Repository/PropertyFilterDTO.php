<?php

namespace App\Features\Properties\Property\DTO\Repository;

final readonly class PropertyFilterDTO
{
    /** @param PropertyFilterValueDTO[] $values */
    public function __construct(
        public string $featureCode,
        public string $valueCode,
        public string $valueName,
        public array $values,
    ) {
    }
}