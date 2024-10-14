<?php

namespace App\Tests\Helper\Integration\Properties;

use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;

class PropertyValueHelper
{
    public const CODE = 'QZG1G';
    public static function createPropertyValueMessageDTO(array $propertyNames): PropertyValueMessageDTO
    {
        return new PropertyValueMessageDTO(
            self::CODE,
                $propertyNames
        );
    }
}