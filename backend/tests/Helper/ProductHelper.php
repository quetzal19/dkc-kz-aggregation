<?php

namespace App\Tests\Helper;

class ProductHelper
{
    public const PRODUCT_CODE = "R5ST0231";

    public const MISSING_PRODUCT_CODE_VALIDATION_ERROR = [
        "name" => "productCode",
        "message" => "Не может быть пустым",
    ];

    public const RESPONSE_ACCESSORY_PRODUCT_FIELDS = ['code'];
}