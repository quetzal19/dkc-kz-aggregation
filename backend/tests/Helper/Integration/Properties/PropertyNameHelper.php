<?php

namespace App\Tests\Helper\Integration\Properties;

use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use App\Helper\Enum\LocaleType;

class PropertyNameHelper
{
    public const NAME = "Длина";
    public const UPDATE_NAME = "Объем";

    public static function createPropertyNameMessageDTO(
        string $locale,
        string $name = self::NAME
    ): PropertyNameMessageDTO {
        return new PropertyNameMessageDTO(
            $name,
            $locale,
        );
    }

    public static function initPropertyNameByLocales(string $name = self::NAME): array
    {
        $locales = LocaleType::getNamesLocale();
        $propertyNames = [];

        foreach ($locales as $locale) {
            $propertyNames[] = self::createPropertyNameMessageDTO($locale, $name);
        }

        return $propertyNames;
    }
}