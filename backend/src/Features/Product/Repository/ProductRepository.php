<?php

namespace App\Features\Product\Repository;

use App\Document\{Product\Product, Properties\PropertyValue, Section\Section};
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

class ProductRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getSortedProperties(
        array $propertiesValue,
        array $sectionCodes,
        array $sortValues,
        string $locale
    ): \Iterator {
        $builder = $this->createAggregationBuilder();

        $propertiesCode = array_keys($propertiesValue);

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
                $builder->expr()->regexMatch('$$property', implode('|', $propertiesCode))
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

//        $orConditions = [];
//        foreach ($propertiesValue as $property => $values) {
//            if (!empty($values) && is_array($values)) {
//                foreach ($values as $value) {
//                    $orConditions[] = [
//                        '$and' => [
//                            ['featureCode' => $property],
//                            ['valueCode' => $value]
//                        ]
//                    ];
//                }
//            }
//        }

//        if (!empty($orConditions)) {
//            $builder->match()->addAnd(
//                ['$or' => $orConditions]
//            );
//        }

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

        $builder
            ->addFields()
            ->field('index')
            ->expression([
                '$indexOfArray' => [$sortValues, '$_id']
            ]);

        $builder->sort('index', 1);

        return $builder->getAggregation()->getIterator();
    }
}