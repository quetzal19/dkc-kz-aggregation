<?php

namespace App\Enum;

enum SortOrder: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public function getIntValue(): int
    {
        return match ($this) {
            self::ASC => 1,
            self::DESC => -1,
        };
    }
}
