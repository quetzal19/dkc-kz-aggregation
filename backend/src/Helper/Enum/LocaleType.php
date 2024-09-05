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

        return match ($value) {
            'en' => LocaleType::LOCALE_EN,
            'kz' => LocaleType::LOCALE_KZ,
            default => LocaleType::LOCALE_RU
        };
    }
}
