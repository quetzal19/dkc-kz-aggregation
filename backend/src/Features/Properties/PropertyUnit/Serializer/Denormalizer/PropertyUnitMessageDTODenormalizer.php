<?php

namespace App\Features\Properties\PropertyUnit\Serializer\Denormalizer;

use App\Features\Properties\PropertyUnit\DTO\Message\PropertyUnitMessageDTO;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class PropertyUnitMessageDTODenormalizer implements DenormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'map.property.name.dto.denormalizer')]
        private DenormalizerInterface $propertyNameDenormalizer
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $dtoNames = [];
        $code = null;

        if (is_array($data)) {
            if (array_key_exists('names', $data)) {
                foreach ($data['names'] as $names) {
                    $dtoNames[] = $this->propertyNameDenormalizer->denormalize($names, $type, $format, $context);
                }
            }
            if (array_key_exists('code', $data)) {
                $code = $data['code'];
            }
        }

        return new PropertyUnitMessageDTO($code, $dtoNames);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === PropertyUnitMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [PropertyUnitMessageDTO::class => true];
    }
}