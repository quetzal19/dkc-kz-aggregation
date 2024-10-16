<?php

namespace App\Tests\Helper\Unit\DTO\Message;

class ProductHelper
{

    public const NOT_BLANK_FIELDS = ['locale', 'id', 'code', 'sectionId', 'sort', 'etimArtClassId'];

    public const TYPE_FIELDS = [
        'locale',
        'id',
        'code',
        'sectionId',
        'active',
        'sort',
        'weight',
        'volume',
        'etimArtClassId'
    ];

    public const NOT_NULL_FIELDS = ['active'];
}