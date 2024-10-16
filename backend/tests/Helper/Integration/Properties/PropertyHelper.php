<?php

namespace App\Tests\Helper\Integration\Properties;

use App\Features\Properties\Property\DTO\Message\PropertyMessageDTO;

class PropertyHelper
{
    public const CODE = 'AF000001';
    public const UPDATED_CODE = 'AF000002';

    public static function createPropertyMessageDTO(array $propertyNames, string $code = self::CODE): PropertyMessageDTO
    {
        return new PropertyMessageDTO($code, $propertyNames);
    }

}