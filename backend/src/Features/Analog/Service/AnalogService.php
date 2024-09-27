<?php

namespace App\Features\Analog\Service;

use App\Helper\DTO\Data\Product\AnalogAccessoryProductDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDataDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionItemDTO;
use App\Features\Analog\{Filter\AnalogFilter, Repository\AnalogRepository};
use App\Features\Product\Repository\ProductRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\Abstract\AbstractSectionService;

final readonly class AnalogService extends AbstractSectionService
{
    public function __construct(
        private AnalogRepository $analogRepository,
        SectionRepository $sectionRepository,
        ProductRepository $productRepository,
    ) {
        parent::__construct($analogRepository, $sectionRepository, $productRepository);
    }

    public function getAnalogProducts(AnalogFilter $filter, string $locale): AnalogAccessoryProductDTO
    {
        $activeProductsCodeBySections = $this->getActiveProductsBySections(
            $filter->productCode,
            $filter->sectionName,
            $locale
        );

        $analogsCode = $this->analogRepository->getActiveAnalogs(
            $filter->productCode,
            $filter->sectionName,
            $locale,
        );

        return $this->getProductsResult(
            limit: $filter->limit,
            page: $filter->page,
            ids: $analogsCode,
            productCodesBySectionCodes: $activeProductsCodeBySections
        );
    }

    public function getAnalogSections(string $productCode, string $locale): AnalogAccessorySectionDTO
    {
        $sections = $this->analogRepository->getSections($productCode, $locale);

        $sections = array_column($sections, '_id');
        $sections = array_filter($sections);
        sort($sections);

        $sections = array_map(fn($section) => new AnalogAccessorySectionItemDTO($section), $sections);

        return new AnalogAccessorySectionDTO(
            new AnalogAccessorySectionDataDTO($sections)
        );
    }
}