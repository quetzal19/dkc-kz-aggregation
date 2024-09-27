<?php

namespace App\Helper\DTO\Data\Section;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

final readonly class AnalogAccessorySectionDataDTO
{
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: AnalogAccessorySectionItemDTO::class)))]
    public array $sections;

    public function __construct(array $sections)
    {
        $this->sections = $sections;
    }
}