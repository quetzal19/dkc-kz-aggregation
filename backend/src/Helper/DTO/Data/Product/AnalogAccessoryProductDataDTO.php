<?php

namespace App\Helper\DTO\Data\Product;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

final readonly class AnalogAccessoryProductDataDTO
{
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: AnalogAccessoryProductItemDTO::class)))]
    public array $products;
    public int $count;

    public function __construct(array $products, int $count = 0)
    {
        $this->products = $products;
        $this->count = $count;
    }
}