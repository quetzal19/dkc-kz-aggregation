<?php

namespace App\Helper\DTO\Data\Section;

use App\Helper\DTO\Data\Product\AnalogAccessoryProductDataDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

final readonly class AnalogAccessorySectionItemDTO
{
    public function __construct(
        public string $name,
    )
    {
    }
}