<?php

namespace App\Helper\Abstract;

use App\Features\Product\Repository\ProductRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\DTO\Data\Product\{AnalogAccessoryProductDataDTO,
    AnalogAccessoryProductDTO,
    AnalogAccessoryProductItemDTO
};
use App\Helper\Pagination\DTO\PaginationDTO;
use MongoDB\BSON\Regex;

abstract readonly class AbstractSectionService
{
    public function __construct(
        private AbstractSectionServiceDocumentRepository $sectionServiceDocumentRepository,
        protected SectionRepository $sectionRepository,
        protected ProductRepository $productRepository,
    ) {
    }

    protected function getProductsResult(
        int $limit,
        int $page,
        array $ids,
        array $productCodesBySectionCodes
    ): AnalogAccessoryProductDTO {
        $products = array_map(fn($analog) => $analog['_id'], $ids);

        $allProducts = [...$products, ...$productCodesBySectionCodes];
        $allProducts = array_unique(array_filter($allProducts));

        sort($allProducts);
        $count = count($allProducts);

        $allProducts = PaginationDTO::sliceArray(data: $allProducts, page: $page, limit: $limit);

        $allProducts = array_map(fn($code) => new AnalogAccessoryProductItemDTO($code), $allProducts);

        return new AnalogAccessoryProductDTO(data: new AnalogAccessoryProductDataDTO($allProducts, $count));
    }

    protected function getActiveProductsBySections(string $productCode, ?string $sectionName, string $locale): array
    {
        $activeSections = $this->getActiveSectionCodes($productCode, $sectionName, $locale);
        $activeProductsCodeBySections = $this->productRepository->findActiveBySectionCodes($activeSections, $locale);

        return array_column($activeProductsCodeBySections, '_id');
    }

    private function getActiveSectionCodes(string $productCode, ?string $sectionName, string $locale): array
    {
        $sections = $this->sectionServiceDocumentRepository->findActiveSectionsByProductCode(
            $productCode,
            $sectionName,
            $locale
        );

        $allSections = [];
        $sectionRegex = [];
        foreach ($sections as $section) {
            $sectionRegex[] = new Regex(
                implode(',', $section['_id']['fullPath']) . '(,|$)'
            );
            $allSections[] = $section['_id']['sectionCode'];
        }

        $sections = $this->sectionRepository->findChildrenByRegex($sectionRegex, $locale);
        foreach ($sections as $section) {
            if (in_array($section['code'], $allSections)) {
                continue;
            }
            $allSections[] = $section['code'];
        }

        return $allSections;
    }
}