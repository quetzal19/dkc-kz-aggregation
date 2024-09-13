<?php

namespace App\Features\Properties\PropertyFeatureMap\Serializer\Denormalizer;

use App\Features\Properties\PropertyFeatureMap\DTO\Message\{PropertyFeatureMapMessageDTO,
    PropertyFeatureMapPrimaryKeyDTO
};
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class PropertyFeatureMapMessageDTODenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        [$primaryKey, $code] = [
            null,
            null
        ];

        if (is_array($data)) {
            [$primaryKeyName, $etimClassKeyName, $featureKeyName] = [
                'primaryKeys',
                'etimArtClassId',
                'featureCode'
            ];

            if (array_key_exists($primaryKeyName, $data) && is_array($data[$primaryKeyName])
                && array_key_exists($etimClassKeyName, $data[$primaryKeyName])
                && array_key_exists($featureKeyName, $data[$primaryKeyName])
            ) {
                $primaryKey = new PropertyFeatureMapPrimaryKeyDTO(
                    etimArtClassId: $data[$primaryKeyName][$etimClassKeyName],
                    featureCode: $data[$primaryKeyName][$featureKeyName]
                );
            }

            if (array_key_exists('unitCode', $data)) {
                $code = $data['unitCode'];
            }
        }

        return new PropertyFeatureMapMessageDTO(
            unitCode: $code,
            primaryKeys: $primaryKey,
        );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === PropertyFeatureMapMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            PropertyFeatureMapMessageDTO::class => true
        ];
    }
}