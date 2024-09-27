<?php

namespace App\Features\SectionFeature\Serializer\Denormalizer;

use App\Features\SectionFeature\DTO\Message\{SectionFeatureMessageDTO, SectionFeaturePrimaryKeyDTO};
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class SectionFeatureMessageDTODenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $sort = null;
        $primaryKey = null;

        if (is_array($data)) {
            [$primaryKeyName, $sectionCode, $featureKeyName] = [
                'primaryKeys',
                'sectionCode',
                'featureCode'
            ];

            if (array_key_exists($primaryKeyName, $data) && is_array($data[$primaryKeyName])
                && array_key_exists($sectionCode, $data[$primaryKeyName])
                && array_key_exists($featureKeyName, $data[$primaryKeyName])
            ) {
                $primaryKey = new SectionFeaturePrimaryKeyDTO(
                    sectionCode: $data[$primaryKeyName][$sectionCode],
                    featureCode: $data[$primaryKeyName][$featureKeyName]
                );
            }

            if (array_key_exists('sort', $data)) {
                $sort = $data['sort'];
            }
        }

        return new SectionFeatureMessageDTO(
            primaryKeys: $primaryKey,
            sort: $sort,
        );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === SectionFeatureMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            SectionFeatureMessageDTO::class => true
        ];
    }
}