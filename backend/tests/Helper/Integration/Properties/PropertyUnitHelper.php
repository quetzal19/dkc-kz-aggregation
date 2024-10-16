<?php

namespace App\Tests\Helper\Integration\Properties;

use App\Features\Properties\PropertyUnit\DTO\Message\PropertyUnitMessageDTO;

class PropertyUnitHelper
{
    public const CODE = 'SPN60510ZL';

    public const UPDATED_CODE = 'EC002400';

    public static function createPropertyUnitMessageDTO(array $names, string $code = self::CODE): PropertyUnitMessageDTO
    {
        return new PropertyUnitMessageDTO(
            $code, $names
        );
    }
}