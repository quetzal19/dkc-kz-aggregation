<?php

namespace App\Features\Product\Repository;

use Doctrine\ODM\MongoDB\Aggregation\Builder;
use App\Document\{Product\Product, Properties\PropertyValue, Section\Section};
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

class ProductRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function buildBasePipeline(
        array $propertyCodes,
        array $sectionCodes,
        string $locale,
    ): Builder {
        $builder = $this->createAggregationBuilder();

//        $builder
//            ->lookup(Section::class)
//            ->localField('section.$id')
//            ->foreignField('_id')
//            ->alias('section');
//
//        $builder->unwind('$section');
//
//        $builder
//            ->match()
//            ->field('section.code')
//            ->in($sectionCodes);

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
                'valueCode' => '$valueCode',
                'unitCode' => '$unitCode',
                'valueName' => '$filteredName.name',
                "productCode" => '$productCode',
            ])
            ->field('count')
            ->sum(1);

        $builder
            ->group()
            ->field('_id')->expression('$_id.featureCode')
            ->field('count')->sum('$count')
            ->field('values')->push([
                'unitCode' => '$_id.unitCode',
                'valueCode' => '$_id.valueCode',
                'valueName' => '$_id.valueName',
                'productCode' => '$_id.productCode',
            ]);


        return $builder;
    }

    public function getSortedProperties(
        array $propertyCodes,
        array $sectionCodes,
        string $locale
    ): \Iterator {
        $builder = $this->buildBasePipeline($propertyCodes, $sectionCodes, $locale);

        $builder
            ->addFields()
            ->field('index')
            ->expression([
                '$indexOfArray' => [$propertyCodes, '$_id']
            ]);

        $builder->sort('index', 1);

        return $builder->getAggregation()->getIterator();
    }

    public function getProductFilters(
        array $filters,
        array $sectionCodes,
        string $locale,
    ): \Iterator {
        $builder = $this->buildBasePipeline(array_keys($filters), $sectionCodes, $locale);

        $builder
            ->addFields()
            ->field('values')
            ->expression('$values.valueCode')
            ->field('productCode')
            ->expression('$values.productCode');

        $builder
            ->unwind('$values');

        $builder
            ->group()
            ->field('_id')
            ->expression([
                'value' => '$values',
                'featureCode' => '$_id',
                'productCodes' => '$productCode',
            ])
            ->field('productCount')
            ->sum(1);

        $match = $builder->match();
        foreach ($filters as $property => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $match->addOr([
                        '$and' => [
                            ['_id.featureCode' => $property],
                            ['_id.value' => $value]
                        ]
                    ]);
                }
            }
        }

        return $builder->getAggregation()->getIterator();
    }
}