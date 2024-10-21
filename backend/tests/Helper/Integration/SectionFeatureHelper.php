<?php

namespace App\Tests\Helper\Integration;

use App\Features\SectionFeature\DTO\Message\SectionFeatureMessageDTO;
use App\Features\SectionFeature\DTO\Message\SectionFeaturePrimaryKeyDTO;

class SectionFeatureHelper
{
    public const SORT = 0;
    public const UPDATED_SORT = 900;

    public static function createSectionFeatureMessageDTO(string $sectionCode, string $featureCode, int $sort = self::SORT): SectionFeatureMessageDTO
    {
        return new SectionFeatureMessageDTO(
            primaryKeys: new SectionFeaturePrimaryKeyDTO(
                sectionCode: $sectionCode,
                featureCode: $featureCode,
            ),
            sort: $sort,
        );
    }
}