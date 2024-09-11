<?php

namespace App\Features\Property\Serializer\Denormalizer;

use App\Features\Property\DTO\Message\{PropertyMessageDTO, PropertyNameMessageDTO};
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PropertyMessageDTODenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $dtoNames = [];
        if (array_key_exists('names', $data)) {
            foreach ($data['names'] as $names) {
                if (is_array($names) && array_key_exists('name', $names) && array_key_exists('locale', $names)) {
                    $dtoNames[] = new PropertyNameMessageDTO($names['name'], $names['locale']);
                }
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