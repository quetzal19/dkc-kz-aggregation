<?php

namespace App\Tests\Helper\Integration;

use App\Features\ProductFeature\DTO\Message\ProductFeatureMessageDTO;
use App\Features\ProductFeature\DTO\Message\ProductFeaturePrimaryKeyDTO;

class ProductFeatureHelper
{
    public const VALUE = "100";

    public static function createProductFeatureMessageDTO(
        string $productCode,
        string $featureCode,
        ?string $value = null,
        ?string $valueCode = null
    ): ProductFeatureMessageDTO {
        return new ProductFeatureMessageDTO(
            primaryKeys: new ProductFeaturePrimaryKeyDTO(
                productCode: $productCode, featureCode: $featureCode
            ),
            value: $value,
            valueCode: $valueCode
        );
    }
}