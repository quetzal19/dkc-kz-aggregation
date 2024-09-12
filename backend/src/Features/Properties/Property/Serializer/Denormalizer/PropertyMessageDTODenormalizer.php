<?php

namespace App\Features\Properties\Property\Serializer\Denormalizer;

use App\Features\Properties\Property\DTO\Message\{PropertyMessageDTO};
use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class PropertyMessageDTODenormalizer implements DenormalizerInterface
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

        return new PropertyMessageDTO($code, $dtoNames);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === PropertyMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [PropertyMessageDTO::class => true];
    }
}