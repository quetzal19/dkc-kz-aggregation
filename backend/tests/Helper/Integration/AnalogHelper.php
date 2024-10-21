<?php

namespace App\Tests\Helper\Integration;

use App\Features\Analog\DTO\Message\AnalogMessageDTO;

class AnalogHelper
{
    public const ANALOG_ID = 'HL_00012';

    public static function createAnalogMessageDTO(
        string $productElementCode,
        array $categories = [],
        ?string $analogElementCode = null,
        ?string $sectionCode = null,
    ): AnalogMessageDTO {
        return new AnalogMessageDTO(
            id: self::ANALOG_ID,
            elementCode: $productElementCode,
            analogCode: $analogElementCode,
            sectionCode: $sectionCode,
            categoryName: $categories,
        );
    }
}