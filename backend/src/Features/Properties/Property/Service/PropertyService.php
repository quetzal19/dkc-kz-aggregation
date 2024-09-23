<?php

namespace App\Features\Properties\Property\Service;

use App\Document\Properties\Property;
use App\Document\Section\Section;
use App\Features\Product\DTO\Property\ProductPropertyDTO;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Properties\Property\DTO\Repository\{PropertyFilterDTO, PropertyFilterValueDTO};
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
        $section = $this->sectionRepository->findOneBy([
            'code' => $propertyFilter->sectionCode,
            'locale' => $localeInt,
        ]);

        if (!$section) {
            throw new ApiException(
                message: 'Секция не найдена',
                status: Response::HTTP_NOT_FOUND,
            );
        }

        $filters = [];
        $filtersIsEmpty = empty($propertyFilter->filters);
        if (!$filtersIsEmpty) {
            try {
                $filters = json_decode($propertyFilter->filters, true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw new ApiException(
                    message: 'Неверный формат фильтра',
                );
            }
        }

        $childrenSections = $this->sectionRepository->findChildrenByFullPath($section->getFullPath(), $localeInt);
        $allSections = [$section, ...$childrenSections];

        $sectionCodes = array_map(fn(Section $section) => $section->getCode(), $allSections);

        $productsProperties = [];
        if (!$filtersIsEmpty) {
            $groupedProducts = $this->productRepository->getProductFilters(
                filters: $filters,
                sectionCodes: $sectionCodes,
                locale: $locale,
            );

            foreach ($groupedProducts as $groupedProduct) {
                $productProperty = $this->denormalizer->denormalize($groupedProduct['_id'], ProductPropertyDTO::class);

                /** @var ProductPropertyDTO[] $productsProperties */
                $productsProperties[$productProperty->featureCode][$productProperty->value] = $productProperty;
            }
        }

        $properties = $this->propertyRepository->findBySectionCodes($sectionCodes);

        $indexedPropertiesByCode = [];
        $propertiesValue = [];
        foreach ($properties as $property) {
            $indexedPropertiesByCode[$property->getCode()] = $property;
            $propertiesValue[$property->getCode()] = [];
        }
        $propertyCodes = array_keys($propertiesValue);

        $foundedProperties = $this->productRepository->getSortedProperties(
            propertyCodes: $propertyCodes,
            sectionCodes: $sectionCodes,
            locale: $locale
        );

        $filters = [];
        $valueFilters = [];
        foreach ($foundedProperties as $foundedProperty) {
            $propertyFilterDTO = $this->denormalizer->denormalize(
                $foundedProperty,
                PropertyFilterDTO::class,
            );

            if (!array_key_exists($propertyFilterDTO->_id, $indexedPropertiesByCode)) {
                continue;
            }

            /** @var Property $property */
            $property = $indexedPropertiesByCode[$propertyFilterDTO->_id];

            $filter = new PropertyFilterItemResponseDTO(
                code: $property->getCode(),
                name: $property->getNameByLocale($locale),
                count: $propertyFilterDTO->count,
            );

            foreach ($propertyFilterDTO->values as $propertyValue) {
                /** @var PropertyFilterValueDTO $propertyValue */
                $propertyValue = $this->denormalizer->denormalize($propertyValue, PropertyFilterValueDTO::class);

                if (array_key_exists($property->getCode(), $valueFilters) &&
                    array_key_exists($propertyValue->valueCode, $valueFilters[$property->getCode()])
                ) {
                    $value = $valueFilters[$property->getCode()][$propertyValue->valueCode];
                } else {
                    $unit = $property->getUnitNameByCodeAndLocale($propertyValue->unitCode, $localeInt);

                    $value = new PropertyFilterItemValueResponseDTO(
                        code: $propertyValue->valueCode,
                        name: $propertyValue->valueName . ($unit ? " $unit" : ''),
                    );

                    $valueFilters[$property->getCode()][$propertyValue->valueCode] = $value;

                    $filter->addValue($value);
                }
            }

            $filters[] = $filter;
        }

        return new PropertyFilterResponseDTO(
            filters: $filters,
            count: 0
        );
    }
}