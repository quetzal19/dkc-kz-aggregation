<?php

namespace App\Tests\Helper\Integration\Properties;

use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;

class PropertyValueHelper
{
    public const CODE = 'QZG1G';
    public const UPDATED_CODE = 'EC002404';

    public static function createPropertyValueMessageDTO(
        array $propertyNames,
        string $code = self::CODE
    ): PropertyValueMessageDTO {
        return new PropertyValueMessageDTO(
            $code,
            $propertyNames
        );
    }
}