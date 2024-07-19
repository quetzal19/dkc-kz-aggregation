<?php

namespace App\Dto\Api\Request;

use OpenApi\Attributes as OA;

readonly class FeatureDto
{

    #[OA\Property(
        property: 'code',
        description: 'Code of feature',
        type: 'string',
        example: 'FDG43414'
    )]
    public string $code;

    #[OA\Property(
        property: 'valueCode',
        description: 'Code of feature value',
        type: 'string',
        example: 'FDG43414'
    )]
    public string $valueCode;

    #[OA\Property(
        property: 'unitCode',
        description: 'Code of feature unit',
        type: 'string',
        example: 'FDG43414'
    )]
    public string $unitCode;
}
