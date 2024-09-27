<?php

namespace App\Controller\Api\v1;

use App\Features\Properties\Property\Filter\PropertyFilter;
use App\Features\Properties\Property\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/filters/')]
final class FilterController extends AbstractController
{
    #[Route(path: '', methods: ['GET'])]
    public function getFilters(
        Request $request,
        #[MapQueryString] PropertyFilter $filter,
        PropertyService $propertyService,
    ): JsonResponse {
        $locale = $request->getLocale();

        return $this->json($propertyService->getFilters($filter, $locale));
    }
}