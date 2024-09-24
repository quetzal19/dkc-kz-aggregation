<?php

namespace App\Features\Analog\Serializer\Denormalizer;

use App\Features\Analog\DTO\Message\AnalogMessageDTO;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[AutoconfigureTag('serializer.denormalizer')]
final readonly class AnalogMessageDTODenormalizer implements DenormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'map.category.name.dto.denormalizer')]
        private DenormalizerInterface $categoryNameDenormalizer
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $dtoNames = [];
        [$id, $elementCode, $analogCode, $sectionCode] = null;

        if (is_array($data)) {
            if (array_key_exists('categoryName', $data) && is_array($data['categoryName'])) {
                foreach ($data['categoryName'] as $names) {
                    $dtoNames[] = $this->categoryNameDenormalizer->denormalize($names, $type, $format, $context);
                }
            }
            if (array_key_exists('id', $data)) {
                $id = $data['id'];
            }
            if (array_key_exists('elementCode', $data)) {
                $elementCode = $data['elementCode'];
            }
            if (array_key_exists('analogCode', $data)) {
                $analogCode = $data['analogCode'];
            }
            if (array_key_exists('sectionCode', $data)) {
                $sectionCode = $data['sectionCode'];
            }
        }

        return new AnalogMessageDTO(
            id: $id,
            elementCode: $elementCode,
            analogCode: $analogCode,
            sectionCode: $sectionCode,
            categoryName: $dtoNames
        );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === AnalogMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [AnalogMessageDTO::class => true];
    }
}