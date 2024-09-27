<?php

namespace App\Features\Product\DTO\Property;

final readonly class ProductPropertyDTO
{
    public function __construct(
        public string $featureCode,
        public string $valueCode,
        /** @var string[] $productCodes */
        public array $products,
    ) {
    }
}