<?php

namespace App\Tests\Helper\Api;

class PaginationHelper
{

    public const INVALID_PAGE_VALIDATION_ERROR = [
        "name" => "page",
        "message" => "Некорректное значение, должно быть числом"
    ];

    public static function invalidLimitParam(string $value): array
    {
        return [
            "name" => "limit",
            "message" => "Значение $value должно быть от 1 до 100"
        ];
    }
}