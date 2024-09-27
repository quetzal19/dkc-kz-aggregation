<?php

namespace App\Features\Properties\PropertyValue\Serializer\Denormalizer;

use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class PropertyValueMessageDTODenormalizer implements DenormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'map.property.name.dto.denormalizer')]
        private DenormalizerInterface $propertyNameDenormalizer
    )
    {

    }
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $dtoNames = [];
        if (array_key_exists('names', $data)) {
            foreach ($data['names'] as $names) {
                $dtoNames[] = $this->propertyNameDenormalizer->denormalize($names, $type, $format, $context);
            }
        }

        $code = null;
        if (array_key_exists('code', $data)) {
            $code = $data['code'];
        }

        return new PropertyValueMessageDTO($code, $dtoNames);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === PropertyValueMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [PropertyValueMessageDTO::class => true];
    }
}