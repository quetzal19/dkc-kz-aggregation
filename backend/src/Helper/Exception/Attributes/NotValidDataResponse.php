<?php

namespace App\Helper\Exception\Attributes;

use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

#[\Attribute]
final class NotValidDataResponse extends OA\Response
{
    public function __construct()
    {
        parent::__construct(
            response: Response::HTTP_BAD_REQUEST,
            description: 'Некорректный запрос (ошибки валидации / некорректный формат запроса).',
            content: new OA\JsonContent(ref: '#components/schemas/ApiExternalError'),
        );
    }
}
