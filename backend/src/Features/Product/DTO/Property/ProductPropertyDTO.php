<?php

namespace App\Features\Product\DTO\Property;

final readonly class ProductPropertyDTO
{
    public function __construct(
        public string $value,
        public string $featureCode,
        /** @var string[] $productCodes */
        public array $productCodes,
        public int $count,
    ) {
    }
}