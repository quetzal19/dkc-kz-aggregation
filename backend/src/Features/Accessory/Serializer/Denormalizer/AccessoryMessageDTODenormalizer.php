<?php

namespace App\Features\Accessory\Serializer\Denormalizer;

use App\Features\Accessory\DTO\Message\AccessoryMessageDTO;
use Symfony\Component\DependencyInjection\Attribute\{AutoconfigureTag, Autowire};
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[AutoconfigureTag('serializer.denormalizer')]
final readonly class AccessoryMessageDTODenormalizer implements DenormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'map.category.name.dto.denormalizer')]
        private DenormalizerInterface $categoryNameDenormalizer
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $dtoNames = [];
        [$id, $elementCode, $accessoryCode, $sectionCode] = null;

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
            if (array_key_exists('accessoryCode', $data)) {
                $accessoryCode = $data['accessoryCode'];
            }
            if (array_key_exists('sectionCode', $data)) {
                $sectionCode = $data['sectionCode'];
            }
        }

        return new AccessoryMessageDTO(
            id: $id,
            elementCode: $elementCode,
            accessoryCode: $accessoryCode,
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
        return $type === AccessoryMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [AccessoryMessageDTO::class => true];
    }
}