<?php

namespace App\Tests\Helper\Api;

final readonly class SectionHelper
{
    public const SECTION_CODE = "103";
    public const RESPONSE_ACCESSORY_SECTIONS_FIELDS = ['name'];

    public const MISSING_SECTION_CODE_VALIDATION_ERROR = [
        "name" => "sectionCode",
        "message" => "Не может быть пустым"
    ];


}