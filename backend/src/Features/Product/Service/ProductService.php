<?php

namespace App\Features\Product\Service;

use App\Document\Section\Section;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\DTO\Data\Product\{AnalogAccessoryProductDataDTO,
    AnalogAccessoryProductDTO,
    AnalogAccessoryProductItemDTO
};
use App\Helper\Enum\LocaleType;
use App\Helper\Exception\ApiException;
use App\Helper\Pagination\DTO\PaginationDTO;
use App\Features\Product\{Filter\ProductFilter, Repository\ProductRepository};
use Symfony\Component\HttpFoundation\Response;

final readonly class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private SectionRepository $sectionRepository,
    ) {
    }

    public function getProducts(ProductFilter $filter, string $locale): AnalogAccessoryProductDTO
    {
        /** @var Section $section */
        $section = $this->sectionRepository->findActiveSection($filter->sectionCode, $locale);

        if (!$section) {
            throw new ApiException(
                message: 'Секция не найдена или не активна',
                status: Response::HTTP_NOT_FOUND,
            );
        }

        $filters = [];
        if (!empty($filter->filters)) {
            $filters = json_decode($filter->filters, true);
        }

        $childrenSections = $this->sectionRepository->findChildrenByFullPath(
            $section->getFullPath(),
            LocaleType::fromString($locale)->value
        );

        $sectionId = array_map(fn(Section $section) => $section->getId(), [$section, ...$childrenSections]);

        $productCodes = $this->productRepository->findActiveBySectionCodes($sectionId, $filters, $locale);
        $count = count($productCodes);

        $productCodes = PaginationDTO::sliceArray(data: $productCodes, page: $filter->page, limit: $filter->limit);

        $productsDTO = array_map(
            fn(array $productCode) => new AnalogAccessoryProductItemDTO($productCode['_id']),
            $productCodes
        );

        return new AnalogAccessoryProductDTO(
            data: new AnalogAccessoryProductDataDTO(
                products: $productsDTO,
                count: $count
            )
        );
    }
}