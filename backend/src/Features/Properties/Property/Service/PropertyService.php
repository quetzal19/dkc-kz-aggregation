<?php

namespace App\Features\Properties\Property\Service;

use App\Document\Properties\Property;
use App\Document\Section\Section;
use App\Features\Product\DTO\Property\ProductPropertyDTO;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Properties\Property\DTO\Repository\PropertyFilterDTO;
use App\Features\Properties\Property\DTO\Http\Response\{PropertyFilterItemResponseDTO,
    PropertyFilterItemValueResponseDTO,
    PropertyFilterResponseDTO
};
use App\Features\Properties\Property\Filter\PropertyFilter;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\{Enum\LocaleType, Exception\ApiException};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class PropertyService
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private PropertyRepository $propertyRepository,
        private ProductRepository $productRepository,
        private DenormalizerInterface $denormalizer,
    ) {
    }

    public function getFilters(PropertyFilter $propertyFilter, string $locale): PropertyFilterResponseDTO
    {
        $localeInt = LocaleType::fromString($locale)->value;

        /** @var Section $section */
        $section = $this->sectionRepository->findActiveSection($propertyFilter->sectionCode, $locale);

        if (!$section) {
            throw new ApiException(
                message: 'Секция не найдена или не активна',
                status: Response::HTTP_NOT_FOUND,
            );
        }

        $filters = [];
        $filtersIsEmpty = empty($propertyFilter->filters);

        if (!$filtersIsEmpty) {
            $filters = json_decode($propertyFilter->filters, true);
        }

        $childrenSections = $this->sectionRepository->findChildrenByFullPath($section->getFullPath(), $localeInt);
        $sectionCodes = array_map(fn(Section $section) => $section->getCode(), [$section, ...$childrenSections]);

        /** @var ProductPropertyDTO[] $productsProperties */
        $productsProperties = [];
        if (!$filtersIsEmpty) {
            $groupedProducts = $this->productRepository->getProductFilters(
                filters: $filters,
                sectionCodes: $sectionCodes,
            );

            foreach ($groupedProducts as $groupedProduct) {
                $productsProperties[] = $this->denormalizer->denormalize($groupedProduct['_id'], ProductPropertyDTO::class);
            }
        }

        $properties = $this->propertyRepository->findBySectionCodes($sectionCodes);

        $indexedPropertiesByCode = [];
        $propertiesCodes = [];
        foreach ($properties as $property) {
            $indexedPropertiesByCode[$property->getCode()] = $property;
            $propertiesCodes[] = $property->getCode();
        }

        $foundedProperties = $this->productRepository->getSortedProperties(
            propertyCodes: $propertiesCodes,
            sectionCodes: $sectionCodes,
            locale: $locale
        );

        $filters = [];
        $propertiesFilter = [];

        $totalCount = 0;
        foreach ($foundedProperties as $foundedProperty) {
            $propertyFilterDTO = $this->denormalizer->denormalize(
                $foundedProperty['_id'],
                PropertyFilterDTO::class,
            );

            if (!array_key_exists($propertyFilterDTO->featureCode, $indexedPropertiesByCode)) {
                continue;
            }

            /** @var Property $property */
            $property = $indexedPropertiesByCode[$propertyFilterDTO->featureCode];

            if (array_key_exists($property->getCode(), $propertiesFilter)) {
                $filter =  $propertiesFilter[$property->getCode()];
            } else {
                $filter = new PropertyFilterItemResponseDTO(
                    code: $property->getCode(),
                    name: $property->getNameByLocale($locale),
                );

                $filters[] = $filter;
                $propertiesFilter[$property->getCode()] = $filter;
            }

            $unit = $property->getUnitNameByCodeAndLocale($propertyFilterDTO->unitCode, $localeInt);

            $value = new PropertyFilterItemValueResponseDTO(
                code: $propertyFilterDTO->valueCode,
                name: $propertyFilterDTO->valueName . ($unit ? " $unit" : ''),
            );

            if(!$filtersIsEmpty) {
                $countProducts = 0;
                foreach ($productsProperties as $productsProperty) {
                    $intersectedProducts = array_intersect($productsProperty->products, $propertyFilterDTO->productCodes);
                    $countProducts += count($intersectedProducts);
                }
            } else {
                $countProducts = count($propertyFilterDTO->productCodes);
            }

            $value
                ->setCount($countProducts)
                ->setEnabledFromBoolean($countProducts > 0);

            $totalCount += $countProducts;

            $filter->addValue($value);
        }

        return new PropertyFilterResponseDTO(
            filters: $filters,
            count: $totalCount
        );
    }
}