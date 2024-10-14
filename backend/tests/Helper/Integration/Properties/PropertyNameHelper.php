<?php

namespace App\Tests\Helper\Integration\Properties;

use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use App\Helper\Enum\LocaleType;

class PropertyNameHelper
{
    public const NAME = "Длина";

    public static function createPropertyNameMessageDTO(string $locale): PropertyNameMessageDTO
    {
        return new PropertyNameMessageDTO(
            self::NAME,
            $locale,
        );
    }

    public static function initPropertyNameByLocales(): array
    {
        $locales = LocaleType::getNamesLocale();
        $propertyNames = [];

        foreach ($locales as $locale) {
            $propertyNames[] = self::createPropertyNameMessageDTO($locale);
        }

        return $propertyNames;
    }
}