<?php

namespace App\Tests\Helper\Integration;

use App\Features\Accessory\DTO\Message\AccessoryMessageDTO;

class AccessoryHelper
{
    public const ACCESSORY_ID = 'HL_00015';

    public static function createAccessoryMessageDTO(
        string $productElementCode,
        array $categories = [],
        ?string $accessoryCode = null,
        ?string $sectionCode = null,
    ): AccessoryMessageDTO {
        return new AccessoryMessageDTO(
            id: self::ACCESSORY_ID,
            elementCode: $productElementCode,
            accessoryCode: $accessoryCode,
            sectionCode: $sectionCode,
            categoryName: $categories,
        );
    }
}