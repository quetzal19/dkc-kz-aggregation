<?php

namespace App\Controller\Api\v1;

use App\Helper\DTO\Data\Product\AnalogAccessoryProductDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Helper\Exception\Attributes\{NotFoundResponse, NotValidDataResponse};
use App\Helper\Pagination\Attributes\{LimitParameter, PageParameter};
use App\Features\Product\{Filter\ProductFilter, Service\ProductService};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Product')]
#[Route('/products/')]
final class ProductController extends AbstractController
{
    #[OA\Get(
        summary: 'Получение списка продуктов',
        parameters: [
            new OA\Parameter(
                name: 'sectionCode',
                description: 'Код раздела',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'ABDLM2'
            ),
            new OA\Parameter(
                name: 'filters',
                description: 'Фильтры',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: '{"code": ["value"]}'),
            ),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Success',
        content: new OA\JsonContent(ref: new Model(type: AnalogAccessoryProductDTO::class))
    )]
    #[LimitParameter]
    #[PageParameter]
    #[NotValidDataResponse]
    #[NotFoundResponse]
    #[Route('', name: 'api_v1_products', methods: [Request::METHOD_GET])]
    public function getProducts(
        Request $request,
        #[MapQueryString] ProductFilter $filter,
        ProductService $productService
    ): JsonResponse {
        $locale = $request->getLocale();

        return $this->json($productService->getProducts($filter, $locale));
    }
}