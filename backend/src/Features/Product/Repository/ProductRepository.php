<?php

namespace App\Features\Product\Repository;

use App\Helper\Enum\LocaleType;
use Doctrine\ODM\MongoDB\Aggregation\Builder;
use App\Document\{Product\Product, Properties\PropertyValue, Section\Section};
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

class ProductRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findActiveBySectionCodes(array $sectionCodes, array $filters, string $locale): array
    {
        $builder = $this->createAggregationBuilder();

        $builder
            ->lookup(Section::class)
            ->localField('section.$id')
            ->foreignField('_id')
            ->alias('section');

        $builder->unwind('$section');

        $builder
            ->match()
            ->field('section.code')
            ->in($sectionCodes)
            ->field('active')
            ->equals(true)
            ->field('locale')
            ->equals(LocaleType::fromString($locale)->value);


//        if (!empty($filters)) {
//            $match = $builder->match();
//
//            foreach ($filters as $property => $values) {
//                foreach ($values as $value) {
//                    $match->addOr([
//                        'property' => ['$regex' => "$property:$value:"]
//                    ]);
//                }
//            }
//        }

        $builder
            ->project()
            ->excludeFields(['_id'])
            ->includeFields(['code'])
            ->group()
            ->field('_id')
            ->expression('$code');

        return $builder->getAggregation()->getIterator()->toArray();
    }

    public function buildBasePipeline(
        array $propertyCodes,
        array $sectionCodes,
    ): Builder {
        $builder = $this->createAggregationBuilder();

        $builder
            ->lookup(Section::class)
            ->localField('section.$id')
            ->foreignField('_id')
            ->alias('section');

        $builder->unwind('$section');

        $builder
            ->match()
            ->field('section.code')
            ->in($sectionCodes);

        $builder
            ->project()
            ->field('productCode')
            ->expression('$code')
            ->field('property')
            ->field('properties')
            ->filter(
                '$property',
                'property',
                $builder->expr()->regexMatch('$$property', implode('|', $propertyCodes))
            );

        $builder
            ->addFields()
            ->field('size')
            ->size('$properties');

        $builder
            ->match()
            ->field('size')
            ->gt(0);

        $builder
            ->unwind('$properties')
            ->addFields()
            ->field('propertyArray')
            ->expression([
                '$split' => ['$properties', ':']
            ])
            ->addFields()
            ->field('featureCode')
            ->expression([
                '$arrayElemAt' => ['$propertyArray', 0]
            ])
            ->field('valueCode')
            ->expression([
                '$arrayElemAt' => ['$propertyArray', 1]
            ])
            ->field('unitCode')
            ->expression([
                '$arrayElemAt' => ['$propertyArray', 2]
            ]);

        $builder
            ->project()
            ->includeFields(['productCode', 'featureCode', 'valueCode', 'unitCode'])
            ->field('length')
            ->strLenCP('$valueCode');

        $builder
            ->match()
            ->field('length')
            ->gt(0);

        return $builder;
    }

    public function getSortedProperties(
        array $propertyCodes,
        array $sectionCodes,
        string $locale
    ): \Iterator {
        $builder = $this->buildBasePipeline($propertyCodes, $sectionCodes);

        $builder
            ->lookup(PropertyValue::class)
            ->let(['valueCode' => '$valueCode'])
            ->pipeline([
                [
                    '$match' => [
                        '$expr' => [
                            '$or' => [
                                ['$eq' => ['$_id', '$$valueCode']],
                                ['$eq' => ['$code', '$$valueCode']]
                            ]
                        ]
                    ]
                ]
            ])
            ->alias('propertyValue');

        $builder
            ->unwind('$propertyValue')
            ->addFields()
            ->field('filteredName')
            ->filter('$propertyValue.names', 'name', $builder->expr()->eq('$$name.locale', $locale))
            ->unwind('$filteredName');

        $builder
            ->group()
            ->field('_id')
            ->expression([
                'featureCode' => '$featureCode',
                'valueName' => '$filteredName.name',
                'valueCode' => '$valueCode',
                'unitCode' => '$unitCode',
            ])
            ->field('productCodes')
            ->push('$productCode');

        $builder
            ->addFields()
            ->field('index')
            ->expression([
                '$indexOfArray' => [$propertyCodes, '$_id.featureCode'],
            ]);

        $builder->sort('index', 1);

        $builder
            ->project()
            ->field('_id')
            ->expression([
                'featureCode' => '$_id.featureCode',
                'valueCode' => '$_id.valueCode',
                'valueName' => '$_id.valueName',
                'unitCode' => '$_id.unitCode',
                'productCodes' => '$productCodes',
            ]);

        return $builder->getAggregation()->getIterator();
    }

    public function getProductFilters(
        array $filters,
        array $sectionCodes,
    ): \Iterator {
        $builder = $this->buildBasePipeline(array_keys($filters), $sectionCodes);

        $builder
            ->group()
            ->field('_id')
            ->expression([
                'featureCode' => '$featureCode',
                'valueCode' => '$valueCode',
            ])
            ->field('products')
            ->push('$productCode');

        $builder
            ->project()
            ->field('_id')
            ->expression([
                'featureCode' => '$_id.featureCode',
                'valueCode' => '$_id.valueCode',
                'products' => '$products',
            ]);

        $match = $builder->match();
        foreach ($filters as $property => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $match->addAnd([
                        '$and' => [
                            ['_id.featureCode' => $property],
                            ['_id.valueCode' => $value]
                        ]
                    ]);
                }
            }
        }

        return $builder->getAggregation()->getIterator();
    }
}