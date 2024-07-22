<?php

namespace App\Controller;

use App\Dto\Api\Response\TestResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ProductsController extends AbstractController
{

    #[Route('/products', name: 'products', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает список товаров',
        content: new Model(type: TestResponse::class)
    )]
    public function add(): JsonResponse
    {
        $testDTO = new TestResponse('bar');

        return new JsonResponse($testDTO->toArray());
    }
}
