<?php

namespace App\Features\Product\Repository;

use App\Helper\Enum\LocaleType;
use Doctrine\ODM\MongoDB\Aggregation\Builder;
use App\Document\{Product\Product};
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

class ProductRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findActiveBySectionCodes(array $sectionId, array $filters, string $locale): array
    {
        $builder = $this->createAggregationBuilder();

        $builder
            ->match()
                ->field('section.$id')->in($sectionId)
                ->field('active')->equals(true)
                ->field('locale')->equals(LocaleType::fromString($locale)->value);


        if (!empty($filters)) {
            $match = $builder->match();

            foreach ($filters as $property => $values) {
                foreach ($values as $value) {
                    $match->addOr([
                        'property' => ['$regex' => "$property:$value:"]
                    ]);
                }
            }
        }

        $builder
            ->project()
                ->excludeFields(['_id'])
                ->includeFields(['code'])
            ->group()
                ->field('_id')->expression('$code');

        return $builder->getAggregation()->getIterator()->toArray();
    }

    public function buildBasePipeline(
        array $propertyCodes,
        array $sectionIds,
    ): Builder {
        $builder = $this->createAggregationBuilder();

        $builder
            ->match()
                ->field('section.$id')
                    ->in($sectionIds)
                ->field('property')
                    ->notEqual([]);

        $builder
            ->project()
                ->field('productCode')
                    ->expression('$code')
                ->field('property')
                    ->expression('$property');

        $builder
            ->unwind('$property')
            ->addFields()
                ->field('propertyArray')
                    ->expression([
                        '$split' => ['$property', ':']
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
            ->match()
                ->field('valueCode')->notEqual('')
                ->field('featureCode')->in($propertyCodes)
            ->project()
                ->includeFields(['productCode', 'featureCode', 'valueCode', 'unitCode']);

        return $builder;
    }

    public function getSortedProperties(
        array $propertyCodes,
        array $sectionIds,
    ): array {
        $builder = $this->buildBasePipeline($propertyCodes, $sectionIds);

        $builder
            ->group()
                ->field('_id')
                    ->expression([
                        'featureCode' => '$featureCode',
                        'valueCode' => '$valueCode',
                        'unitCode' => '$unitCode',
                    ])
                ->field('productCodes')->push('$productCode');

        $builder
            ->project()
                ->field('_id')
                    ->expression([
                        'featureCode' => '$_id.featureCode',
                        'valueCode' => '$_id.valueCode',
                        'unitCode' => '$_id.unitCode',
                        'productCodes' => '$productCodes',
                    ]);


        $builder
            ->addFields()
                ->field('index')
                    ->expression([
                        '$indexOfArray' => [$propertyCodes, '$_id.featureCode'],
                    ]);

        $builder->sort('index', 1);

        return $builder->getAggregation()->getIterator()->toArray();
    }

    public function getProductFilters(
        array $filters,
        array $sectionIds,
    ): \Iterator {
        $builder = $this->buildBasePipeline(array_keys($filters), $sectionIds);

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