<?php

namespace App\Helper\DTO\Data\Section;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

final readonly class AnalogAccessorySectionDTO
{
    #[OA\Property(ref: new Model(type: AnalogAccessorySectionDataDTO::class), type: 'object')]
    public AnalogAccessorySectionDataDTO $data;
    public array $errors;

    public function __construct(
        AnalogAccessorySectionDataDTO $data,
        array $errors = []
    )
    {
        $this->data = $data;
        $this->errors = $errors;
    }
}