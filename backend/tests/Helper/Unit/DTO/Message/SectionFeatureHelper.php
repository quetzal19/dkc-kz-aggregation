<?php

namespace App\Tests\Helper\Unit\DTO\Message;

class SectionFeatureHelper
{
    public const NOT_BLANK_FIELDS = ["primaryKeys"];
    public const TYPE_FIELDS = ["primaryKeys", "sort"];
    public const INTEGER_VALID_FIELDS = ["sort"];

    public const PRIMARY_FIELDS = ["sectionCode", "featureCode"];
}