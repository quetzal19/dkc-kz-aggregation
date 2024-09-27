<?php

namespace App\Helper\Exception\Attributes;

use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

#[\Attribute]
final class NotFoundResponse extends OA\Response
{
    public function __construct(?string $description = null)
    {
        parent::__construct(
            response: Response::HTTP_NOT_FOUND,
            description: $description ?? 'Сущность не найдена.',
            content: new OA\JsonContent(ref: '#components/schemas/ApiExternalError'),
        );
    }
}
