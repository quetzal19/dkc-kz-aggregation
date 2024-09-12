<?php

namespace App\Features\Properties\PropertyName\Serializer\Denormalizer;

use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class PropertyNameDTODenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        [$code, $locale] = [null, null];
        if (is_array($data) && array_key_exists('name', $data) && array_key_exists('locale', $data)) {
            $code = $data['name'];
            $locale = $data['locale'];
        }

        return new PropertyNameMessageDTO($code, $locale);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === PropertyNameMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [PropertyNameMessageDTO::class => true];
    }
}