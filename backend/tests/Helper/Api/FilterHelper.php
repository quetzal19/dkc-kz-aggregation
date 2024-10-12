<?php

namespace App\Tests\Helper\Api;

class FilterHelper
{
    public const RESPONSE_FILTER_FIELDS = ['code', 'name', 'type', 'values'];


    public const FILTERS_INCORRECT_FORMAT_VALIDATION_ERROR = [
        'name' => 'filters',
        'message' => 'Неверный формат фильтра',
    ];
}