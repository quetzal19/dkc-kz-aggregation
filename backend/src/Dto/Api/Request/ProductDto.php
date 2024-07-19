<?php

namespace App\Dto\Api\Request;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

readonly class ProductDto
{
    #[OA\Property(
        description: 'Код продукта',
        type: 'string',
        example: 'SNS23510'
    )]
    public string $code;

    public string $sectionCode;

    #[OA\Property(
        description: 'Название продукта',
        type: 'string',
        example: 'Лоток 100х35 L2000'
    )]
    public string $name;

    public string $weight;

    public string $volume;

    #[OA\Property(
        description: 'Список возможных фильтров',
        type: 'array',
        items: new OA\Items(
            ref: new Model(type: FeatureDto::class)
        )
    )]
    public array $features;
}
