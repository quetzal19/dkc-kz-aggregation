<?php

namespace App\Features\Analog\Service;

use App\Document\Product\Product;
use App\Helper\DTO\Data\Product\AnalogAccessoryProductDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDataDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionItemDTO;
use App\Helper\Enum\LocaleType;
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
        /** @var Product $product */
        $product = $this->productRepository->findOneBy(
            [
                'code' => $filter->productCode,
                'locale' => LocaleType::fromString($locale)->value
            ]
        );

        if (empty($product)) {
            return $this->getProductsResult(
                limit: $filter->limit,
                page: $filter->page,
                ids: [],
                productCodesBySectionCodes: []
            );
        }

        $activeProductsCodeBySections = $this->getActiveProductsBySections(
            $product->getId(),
            $filter->sectionName,
            $locale
        );

        $analogsCode = $this->analogRepository->getActiveAnalogs(
            $product->getId(),
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