<?php

namespace App\Helper\Pagination\Attributes;

use OpenApi\Attributes as OA;

#[\Attribute]
final class PageParameter extends OA\Parameter
{
    public function __construct(?bool $required = false)
    {
        parent::__construct(
            name: 'page',
            description: 'Номер страницы',
            in: 'query',
            required: $required,
            schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
        );
    }
}
