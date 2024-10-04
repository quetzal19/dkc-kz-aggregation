<?php

namespace App\Features\Accessory\Service;

use App\Document\Product\Product;
use App\Helper\DTO\Data\Product\AnalogAccessoryProductDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDataDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionDTO;
use App\Helper\DTO\Data\Section\AnalogAccessorySectionItemDTO;
use App\Helper\Enum\LocaleType;
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

        $accessoriesCode = $this->accessoryRepository->getActiveAccessories(
            $product->getId(),
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
        $sections = [];

        /** @var Product $product */
        $product = $this->productRepository->findOneBy(
            [
                'code' => $productCode,
                'locale' => LocaleType::fromString($locale)->value
            ]
        );

        if (empty($product)) {
            return new AnalogAccessorySectionDTO(
                new AnalogAccessorySectionDataDTO($sections)
            );
        }

        $sections = $this->accessoryRepository->getSections($product->getId(), $locale);

        $sections = array_column($sections, '_id');
        $sections = array_filter($sections);
        sort($sections);

        $sections = array_map(fn($section) => new AnalogAccessorySectionItemDTO($section), $sections);

        return new AnalogAccessorySectionDTO(
            new AnalogAccessorySectionDataDTO($sections)
        );
    }
}