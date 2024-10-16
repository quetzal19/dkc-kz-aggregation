<?php

namespace App\Tests\Helper\Integration\Properties;

use App\Features\Properties\PropertyFeatureMap\DTO\Message\PropertyFeatureMapMessageDTO;
use App\Features\Properties\PropertyFeatureMap\DTO\Message\PropertyFeatureMapPrimaryKeyDTO;

class PropertyFeatureMapHelper
{
    public static function createPropertyFeatureMapMessageDTO(
        string $unitCode,
        string $etimArtClassId,
        string $featureCode
    ): PropertyFeatureMapMessageDTO {
        $primary = self::createPropertyFeatureMapPrimaryKeyDTO($featureCode, $etimArtClassId);
        return new PropertyFeatureMapMessageDTO(
            $unitCode, $primary
        );
    }

    private static function createPropertyFeatureMapPrimaryKeyDTO(
        string $featureCode,
        string $etimArtClassId
    ): PropertyFeatureMapPrimaryKeyDTO {
        return new PropertyFeatureMapPrimaryKeyDTO(
            $etimArtClassId, $featureCode,
        );
    }
}