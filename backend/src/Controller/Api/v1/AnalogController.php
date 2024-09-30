<?php

namespace App\Controller\Api\v1;

use App\Helper\Exception\Attributes\NotFoundResponse;
use App\Helper\Exception\Attributes\NotValidDataResponse;
use App\Helper\DTO\Data\{Product\AnalogAccessoryProductDTO, Section\AnalogAccessorySectionDTO};
use App\Features\Analog\{Filter\AnalogFilter, Service\AnalogService};
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Аналоги')]
#[Route('/analogs')]
final class AnalogController extends AbstractController
{
    public function __construct(
        private readonly AnalogService $analogService,
    ) {
    }

    #[OA\Get(
        summary: 'Список аналогов товара',
        parameters: [
            new OA\Parameter(
                name: 'productCode',
                description: 'Код товара',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'ABDLM2'
            ),
            new OA\Parameter(
                name: 'sectionName',
                description: 'Название раздела',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Контактные блоки'
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Номер страницы',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Количество элементов на странице',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 4, maximum: 100, minimum: 1)
            ),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Success',
        content: new OA\JsonContent(ref: new Model(type: AnalogAccessoryProductDTO::class))
    )]
    #[NotFoundResponse]
    #[NotValidDataResponse]
    #[Route('/products/', name: 'api_v1_analogs_products', methods: [Request::METHOD_GET])]
    public function getAnalogsProducts(
        Request $request,
        #[MapQueryString] AnalogFilter $filter
    ): JsonResponse {
        $locale = $request->getLocale();

        return $this->json($this->analogService->getAnalogProducts($filter, $locale));
    }

    #[OA\Get(
        summary: 'Список разделов аналогов',
        parameters: [
            new OA\Parameter(
                name: 'productCode',
                description: 'Код товара',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'ABDLM2'
            ),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Success',
        content: new OA\JsonContent(ref: new Model(type: AnalogAccessorySectionDTO::class))
    )]
    #[NotFoundResponse]
    #[NotValidDataResponse]
    #[Route('/sections/', name: 'api_v1_analogs_sections', methods: [Request::METHOD_GET])]
    public function getAnalogsSections(
        Request $request,
        #[MapQueryParameter] string $productCode
    ): JsonResponse {
        $locale = $request->getLocale();

        return $this->json($this->analogService->getAnalogSections($productCode, $locale));
    }
}