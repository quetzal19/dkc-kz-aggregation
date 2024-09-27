<?php

namespace App\Helper\DTO\Data\Product;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

final readonly class AnalogAccessoryProductDTO
{
    #[OA\Property(ref: new Model(type: AnalogAccessoryProductDataDTO::class), type: 'object')]
    public AnalogAccessoryProductDataDTO $data;
    public array $errors;

    public function __construct(AnalogAccessoryProductDataDTO $data, array $errors = [])
    {
        $this->data = $data;
        $this->errors = $errors;
    }
}