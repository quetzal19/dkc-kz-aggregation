<?php

namespace App\Tests\Helper\Integration;

use App\Features\Product\DTO\Message\ProductMessageDTO;
use App\Helper\Enum\LocaleType;

class ProductHelper
{
    public const CODE = 'ABD01L';

    public const UPDATE_CODE = 'ABD01L_1';
    public const EXTERNAL_ID = 'ABD01L_0';
    public const ART_CLASS_ID = 'ABL_0';
    public const UPDATED_SORT = 900;

    public static function createProductMessageDTO(
        string $sectionId,
        bool $active = true,
        int $sort = 0,
        string $code = self::CODE,
    ): ProductMessageDTO {
        return new ProductMessageDTO(
            LocaleType::getDefaultLocaleName(),
            self::EXTERNAL_ID,
            $code,
            sectionId: $sectionId,
            active: $active,
            sort: $sort,
            weight: null,
            volume: null,
            etimArtClassId: self::ART_CLASS_ID
        );
    }
}