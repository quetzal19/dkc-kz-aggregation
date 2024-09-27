<?php

namespace App\Features\Accessory\Service;

use App\Helper\DTO\Data\Product\AnalogAccessoryProductDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDataDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionItemDTO;
use App\Features\Accessory\{Filter\AccessoryFilter, Repository\AccessoryRepository};
use App\Features\Product\Repository\ProductRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\Abstract\AbstractSectionService;

final readonly class AccessoryService extends AbstractSectionService
{
    public function __construct(
        private AccessoryRepository $accessoryRepository,
        ProductRepository $productRepository,
        SectionRepository $sectionRepository,
    ) {
        parent::__construct($accessoryRepository, $sectionRepository, $productRepository);
    }

    public function getAccessoryProducts(AccessoryFilter $filter, string $locale): AnalogAccessoryProductDTO
    {
        $activeProductsCodeBySections = $this->getActiveProductsBySections(
            $filter->productCode,
            $filter->sectionName,
            $locale
        );

        $accessoriesCode = $this->accessoryRepository->getActiveAccessories(
            $filter->productCode,
            $filter->sectionName,
            $locale,
        );

        return $this->getProductsResult(
            limit: $filter->limit,
            page: $filter->page,
            ids: $accessoriesCode,
            productCodesBySectionCodes: $activeProductsCodeBySections
        );
    }

    public function getAccessorySections(string $productCode, string $locale): AnalogAccessorySectionDTO
    {
        $sections = $this->accessoryRepository->getSections($productCode, $locale);
        $sections = array_column($sections, '_id');
        $sections = array_filter($sections);
        sort($sections);

        $sections = array_map(fn($section) => new AnalogAccessorySectionItemDTO($section), $sections);

        return new AnalogAccessorySectionDTO(
            new AnalogAccessorySectionDataDTO($sections)
        );
    }
}