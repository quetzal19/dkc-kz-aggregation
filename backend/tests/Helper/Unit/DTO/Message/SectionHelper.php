<?php

namespace App\Tests\Helper\Unit\DTO\Message;

class SectionHelper
{
    public const NOT_BLANK_FIELDS = ["name", "code", "locale", "id"];
    public const TYPE_FIELDS = ["parentId", "active", "sort", "name", "code", "locale", "id"];
    public const NOT_NULL_FIELDS = ["active"];
    public const CHOICES_FIELDS = ["locale"];
}