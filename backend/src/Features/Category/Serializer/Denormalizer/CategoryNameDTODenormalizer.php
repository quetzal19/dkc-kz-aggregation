<?php

namespace App\Features\Category\Serializer\Denormalizer;

use App\Features\Category\DTO\Message\CategoryNameMessageDTO;
use Symfony\Component\DependencyInjection\Attribute\{AsAlias, AutoconfigureTag};
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[AutoconfigureTag('serializer.denormalizer')]
#[AsAlias(id: 'map.category.name.dto.denormalizer')]
final class CategoryNameDTODenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        [$code, $locale] = [null, null];
        if (is_array($data) && array_key_exists('name', $data) && array_key_exists('locale', $data)) {
            $code = $data['name'];
            $locale = $data['locale'];
        }

        return new CategoryNameMessageDTO($code, $locale);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $type === CategoryNameMessageDTO::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [CategoryNameMessageDTO::class => true];
    }
}