<?php

namespace App\Enum;

enum SortType: string
{
    case CODE = 'code';
    case NAME = 'name';
    case WEIGHT = 'weight';
    case VOLUME = 'volume';
}
