<?php

namespace App\Dto;

use App\Dto\Api\Request\FeatureDto;

readonly class Product
{

    public string $code;

    public string $sectionCode;

    public string $name;

    public string $weight;

    public string $volume;

    public array $features;
}
