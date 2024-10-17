<?php

namespace App\Features\Properties\Property\Service;

use App\Document\Properties\Property;
use App\Document\Properties\PropertyValue;
use App\Document\Section\Section;
use App\Features\Product\DTO\Property\ProductPropertyDTO;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Properties\Property\DTO\Repository\PropertyFilterDTO;
use App\Features\Properties\PropertyValue\Repository\PropertyValueRepository;
use App\Features\Properties\Property\DTO\Http\Response\{PropertyFilterItemResponseDTO,
    PropertyFilterItemValueResponseDTO,
    PropertyFilterResponseDTO
};
use App\Features\Properties\Property\Filter\PropertyFilter;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\{Enum\LocaleType, Exception\ApiException};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class PropertyService
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private PropertyRepository $propertyRepository,
        private PropertyValueRepository $propertyValueRepository,
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

        $allSection = [$section, ...$childrenSections];

        $sectionCodes = [];
        $sectionIds = [];
        foreach ($allSection as $section) {
            $sectionCodes[] = $section->getCode();
            $sectionIds[] = $section->getId();
        }

        /** @var ProductPropertyDTO[] $productsProperties */
        $productsProperties = [];
        if (!$filtersIsEmpty) {
            $productsProperties = $this->getProductProperties($filters, $sectionIds);
        }

        $properties = $this->propertyRepository->findBySectionCodes($sectionCodes);

        $indexedPropertiesByCode = [];
        $propertiesCodes = [];
        foreach ($properties as $property) {
            $indexedPropertiesByCode[$property->getCode()] = $property;
            if (!in_array($property->getCode(), $propertiesCodes)) {
                $propertiesCodes[] = $property->getCode();
            }
        }

        $foundedProperties = $this->productRepository->getSortedProperties(
            propertyCodes: $propertiesCodes,
            sectionIds: $sectionIds,
        );

        $foundedProperties = array_column($foundedProperties, '_id');

        $propertyValueNames = $this->getPropertyValueNamesByFoundedProperties($foundedProperties, $locale);

        $filters = [];
        $propertiesFilter = [];

        $totalCount = 0;
        foreach ($foundedProperties as $foundedProperty) {
            $propertyFilterDTO = $this->denormalizer->denormalize(
                $foundedProperty,
                PropertyFilterDTO::class,
            );

            if (!array_key_exists($propertyFilterDTO->featureCode, $indexedPropertiesByCode)) {
                continue;
            }

            /** @var Property $property */
            $property = $indexedPropertiesByCode[$propertyFilterDTO->featureCode];

            if (array_key_exists($property->getCode(), $propertiesFilter)) {
                $filter = $propertiesFilter[$property->getCode()];
            } else {
                $filter = new PropertyFilterItemResponseDTO(
                    code: $property->getCode(),
                    name: $property->getNameByLocale($locale),
                );

                $filters[] = $filter;
                $propertiesFilter[$property->getCode()] = $filter;
            }

            if (!$filtersIsEmpty) {
                $countProducts = 0;
                foreach ($productsProperties as $productsProperty) {
                    $intersectedProducts = array_intersect(
                        $productsProperty->products,
                        $propertyFilterDTO->productCodes
                    );
                    $countProducts += count($intersectedProducts);
                }
            } else {
                $countProducts = count($propertyFilterDTO->productCodes);
            }

            if (array_key_exists($propertyFilterDTO->valueCode, $propertyValueNames)) {
                $valueName = $propertyValueNames[$propertyFilterDTO->valueCode];
            } else {
                continue;
            }

            $value = $this->getPropertyValueFilterDTO($propertyFilterDTO, $valueName, $property, $localeInt);

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

    private function getPropertyValueFilterDTO(
        PropertyFilterDTO $propertyFilterDTO,
        string $valueName,
        Property $property,
        int $localeInt
    ): PropertyFilterItemValueResponseDTO {
        $unit = $property->getUnitNameByCodeAndLocale($propertyFilterDTO->unitCode, $localeInt);

        return new PropertyFilterItemValueResponseDTO(
            code: $propertyFilterDTO->valueCode,
            name: $valueName . ($unit ? " $unit" : ''),
        );
    }

    private function getPropertyValueNamesByFoundedProperties(array $foundedProperties, string $locale): array
    {
        $valuesCodes = array_column($foundedProperties, 'valueCode');

        /** @var PropertyValue[] $values */
        $values = $this->propertyValueRepository->createAggregationBuilder()
            ->hydrate(PropertyValue::class)
            ->match()
                ->addOr([
                    'code' => ['$in' => $valuesCodes],
                ])
                ->addOr([
                    '_id' => ['$in' => $valuesCodes],
                ])
            ->getAggregation()
            ->getIterator();

        $indexedValues = [];
        foreach ($values as $value) {
            $indexedValues[$value->getCodeOrId()] = $value->getNameByLocale($locale)?->getName() ?? '';
        }

        return $indexedValues;
    }

    /**
     * @return ProductPropertyDTO[]
     * @throws ExceptionInterface
     */
    private function getProductProperties(array $filters, array $sectionIds): array
    {
        $groupedProducts = $this->productRepository->getProductFilters(
            filters: $filters,
            sectionIds: $sectionIds,
        );

        $productsProperties = [];
        foreach ($groupedProducts as $groupedProduct) {
            $productsProperties[] = $this->denormalizer->denormalize(
                $groupedProduct['_id'],
                ProductPropertyDTO::class
            );
        }

        return $productsProperties;
    }
}