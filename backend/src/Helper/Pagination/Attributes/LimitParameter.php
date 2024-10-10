<?php

namespace App\Helper\Pagination\Attributes;

use OpenApi\Attributes as OA;

#[\Attribute]
final class LimitParameter extends OA\Parameter
{
    public function __construct(?bool $required = false, int $default = 20)
    {
        parent::__construct(
            name: 'limit',
            description: 'Количество элементов на странице',
            in: 'query',
            required: $required,
            schema: new OA\Schema(type: 'integer', default: $default, maximum: 100, minimum: 1)
        );
    }
}
