<?php

namespace App\Controller\Api\v1;

use App\Features\Properties\Property\DTO\Http\Response\PropertyFilterResponseDTO;
use App\Features\Properties\Property\Filter\PropertyFilter;
use App\Features\Properties\Property\Service\PropertyService;
use App\Helper\Exception\Attributes\NotFoundResponse;
use App\Helper\Exception\Attributes\NotValidDataResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Фильтры')]
#[Route(path: '/filters/')]
final class FilterController extends AbstractController
{
    #[OA\Get(
        summary: 'Получение списков фильтров',
        parameters: [
            new OA\Parameter(
                name: 'sectionCode',
                description: 'Код секции',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string'),
            ),
            new OA\Parameter(
                name: 'filters',
                description: 'Фильтры',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: '{"code": ["value"]}'),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success',
                content: new OA\JsonContent(ref: new Model(type: PropertyFilterResponseDTO::class)),
            )
        ]
    )]
    #[NotFoundResponse]
    #[NotValidDataResponse]
    #[Route(path: '', name: 'api_v1_filter', methods: ['GET'])]
    public function getFilters(
        Request $request,
        #[MapQueryString] PropertyFilter $filter,
        PropertyService $propertyService,
    ): JsonResponse {
        $locale = $request->getLocale();

        return $this->json($propertyService->getFilters($filter, $locale));
    }
}