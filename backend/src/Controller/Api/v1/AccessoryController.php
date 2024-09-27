<?php

namespace App\Controller\Api\v1;

use App\Features\Accessory\Filter\AccessoryFilter;
use App\Features\Accessory\Service\AccessoryService;
use App\Helper\DTO\Data\Product\AnalogAccessoryProductDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDTO;
use App\Helper\Exception\Attributes\NotFoundResponse;
use App\Helper\Exception\Attributes\NotValidDataResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Аксессуары')]
#[Route('/accessories')]
final class AccessoryController extends AbstractController
{
    public function __construct(
        private readonly AccessoryService $accessoryService,
    ) {
    }

    #[OA\Get(
        summary: 'Список акссессуаров товара',
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
    #[Route('/products/', name: 'api_v1_accessories_products', methods: [Request::METHOD_GET])]
    public function getProductsAccessories(
        Request $request,
        #[MapQueryString] AccessoryFilter $filter
    ): JsonResponse
    {
        $locale = $request->getLocale();

        return $this->json($this->accessoryService->getAccessoryProducts($filter, $locale));
    }

    #[OA\Get(
        summary: 'Список разделов аксессуаров',
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
    #[Route('/sections/', name: 'api_v1_accessories_sections', methods: [Request::METHOD_GET])]
    public function getSectionsAccessories(
        Request $request,
        #[MapQueryParameter] string $productCode,
    ): JsonResponse {
        $locale = $request->getLocale();

        return $this->json($this->accessoryService->getAccessorySections($productCode, $locale));
    }
}