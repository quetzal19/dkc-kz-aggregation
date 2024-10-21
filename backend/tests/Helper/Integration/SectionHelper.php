<?php

namespace App\Tests\Helper\Integration;

use App\Features\Section\DTO\Message\SectionMessageDTO;
use App\Helper\Enum\LocaleType;
use App\Helper\Interface\Message\MessageDTOInterface;

class SectionHelper
{
    public const CODE = '08ADE1';
    public const NAME = 'Балки крестовидные';
    public const EXTERNAL_ID = '08ADE1_0';

    public const UPDATED_SORT = 900;

    public const PARENT_CODE = '08ADX2';
    public const PARENT_NAME = 'Балки';
    public const PARENT_EXTERNAL_ID = '08ADX2_0';

    public static function createSectionMessageDTO(
        string $name = self::NAME,
        string $code = self::CODE,
        string $externalId = self::EXTERNAL_ID,
        ?string $parentId = null,
        bool $active = true,
        int $sort = 0,
    ): SectionMessageDTO {
        return new SectionMessageDTO(
            LocaleType::getDefaultLocaleName(),
            parentId: $parentId,
            active: $active,
            sort: $sort,
            name: $name,
            code: $code,
            id: $externalId,
        );
    }
}