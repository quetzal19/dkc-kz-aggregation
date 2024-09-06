<?php

namespace App\Helper\Enum;

enum LocaleType: int
{
    case LOCALE_RU = 10;
    case LOCALE_EN = 20;
    case LOCALE_KZ = 30;

    public static function fromString(string $value): LocaleType
    {
        if (empty($value)) {
            return LocaleType::LOCALE_RU;
        }

        $localeNames = self::getNameLocaleTypes();
        if (!array_key_exists($value, $localeNames)) {
            return LocaleType::LOCALE_RU;
        }

        return $localeNames[$value];
    }

    public static function getNameLocaleTypes(): array
    {
        return [
            'ru' => LocaleType::LOCALE_RU,
            'en' => LocaleType::LOCALE_EN,
            'kz' => LocaleType::LOCALE_KZ
        ];
    }

    public static function getNamesLocale(): array
    {
        return array_keys(self::getNameLocaleTypes());
    }
}
