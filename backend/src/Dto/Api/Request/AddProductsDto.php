<?php

namespace App\Dto\Api\Request;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class AddProductsDto
{
    #[OA\Property(
        property: 'products',
        description: 'Список товаров',
        type: 'array',
        items: new OA\Items(ref: new Model(type: ProductDto::class)),
    )]
    public array $data;
}
