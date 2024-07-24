<?php

namespace App\Service;

use App\Enum\SortOrder;
use App\Enum\SortType;
use App\Repository\ProductRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

class ProductListService
{
    public function __construct(protected ProductRepository $productRepository)
    {
    }

    public function getProducts(
        string $sectionCode,
        array $filters,
        string $sortType = SortType::CODE->value,
        string $sortOrder = SortOrder::ASC->value,
        int $page = 1,
        int $ipp = 10
    ): array {
        try {
            $sortType = SortType::tryFrom($sortType);
            $sortOrder = SortOrder::tryFrom($sortOrder);

            if ($sortType === null) {
                throw new \InvalidArgumentException('Invalid sort type');
            }

            if ($sortOrder === null) {
                throw new \InvalidArgumentException('Invalid sort order');
            }

            return $this->productRepository->findBySectionAndFilters(
                $sectionCode,
                $filters,
                $sortType,
                $sortOrder,
                $page,
                $ipp
            );
        } catch (MongoDBException $e) {
            return [
                'error' => 'Database error:' . $e->getMessage(),
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'error' => 'Invalid argument: ' . $e->getMessage(),
            ];
        }
    }
}
