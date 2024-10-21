<?php

namespace App\Tests\Helper\Integration;

use App\Features\Category\DTO\Message\CategoryNameMessageDTO;
use App\Helper\Enum\LocaleType;

class CategoryNameHelper
{
    public const CATEGORY_NAME = 'Другое';

    public static function initCategoryNameByLocales(string $name = self::CATEGORY_NAME): array
    {
        $locales = LocaleType::getNamesLocale();

        $categories = [];
        foreach ($locales as $locale) {
            $categories[] = new CategoryNameMessageDTO(
                $name, $locale
            );
        }

        return $categories;
    }
}