<?php

namespace App\Controller;

use App\Dto\Api\TestResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api', name: 'api_')]
class ProductsController extends AbstractController
{

    #[Route('/products', name: 'products', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает список Task пользователя в Workspace',
        content: new Model(type: TestResponse::class)
    )]
    public function testApiDoc(): JsonResponse
    {
        $testDTO = new TestResponse('bar');

        return new JsonResponse($testDTO->toArray());
    }
}
