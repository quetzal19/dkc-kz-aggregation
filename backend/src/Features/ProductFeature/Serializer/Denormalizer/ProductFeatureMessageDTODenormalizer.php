<?php

namespace App\Features\ProductFeature\Serializer\Denormalizer;

use App\Features\ProductFeature\DTO\Message\{ProductFeatureMessageDTO, ProductFeaturePrimaryKeyDTO};
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class ProductFeatureMessageDTODenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        [$primaryKey, $value, $code] = [
            null,
            null,
            null
        ];

        if (is_array($data)) {
            [$primaryKeyName, $productKeyName, $featureKeyName] = [
                'primaryKeys',
                'productCode',
                'featureCode'
            ];

            if (array_key_exists($primaryKeyName, $data) && is_array($data[$primaryKeyName])
                && array_key_exists($productKeyName, $data[$primaryKeyName])
                && array_key_exists($featureKeyName, $data[$primaryKeyName])
            ) {
                $primaryKey = new ProductFeaturePrimaryKeyDTO(
                    productCode: $data[$primaryKeyName][$productKeyName],
                    featureCode: $data[$primaryKeyName][$featureKeyName]
                );
            }

            if (array_key_exists('value', $data)) {
                $value = $data['value'];
            }

            if (array_key_exists('valueCode', $data)) {
                $code = $data['valueCode'];
            }
        }

        return new ProductFeatureMessageDTO(
            primaryKeys: $primaryKey,
            value: $value,
            valueCode: $code
        );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === ProductFeatureMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ProductFeatureMessageDTO::class => true
        ];
    }
}