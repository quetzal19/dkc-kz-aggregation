<?php

namespace App\Helper\Abstract;

use MongoDB\BSON\Regex;
use App\Document\{Product\Product, Section\Section};
use App\Helper\Enum\LocaleType;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

abstract class AbstractSectionServiceDocumentRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry, string $documentClass)
    {
        parent::__construct($registry, $documentClass);
    }

    protected function getActiveProductCodesByProperty(string $productCode, ?string $sectionName, string $locale, string $property): array
    {
        $builder = $this->createAggregationBuilder();

        if (!empty($sectionName)) {
            $builder
                ->match()
                ->field('categoryName.name')
                ->equals(new Regex('(^|\s)' . $sectionName . '(\s|$)', 1));
        }

        $builder
            ->lookup(Product::class)
            ->localField('element.$id')
            ->foreignField('_id')
            ->alias('element');

        $builder
            ->match()
            ->field('element.code')
            ->equals($productCode)
            ->field($property)
            ->notEqual(null);

        $builder
            ->lookup(Product::class)
            ->localField( $property . '.$id')
            ->foreignField('_id')
            ->alias($property);

        $builder
            ->match()
            ->field($property . '.active')
            ->equals(true)
            ->field($property . '.locale')
            ->equals(LocaleType::fromString($locale)->value);

        $builder
            ->lookup(Section::class)
            ->localField($property . '.section.$id')
            ->foreignField('_id')
            ->alias('section');

        $builder
            ->unwind('$section');

        $builder
            ->addFields()
            ->field('fullPath')
            ->expression(
                [
                    '$split' => [
                        [
                            '$cond' => [
                                ['$ifNull' => ['$section.path', false]],
                                ['$concat' => ['$section.path', ',', '$section.code']],
                                '$section.code'
                            ]
                        ],
                        ','
                    ]
                ]
            );

        $builder
            ->lookup(Section::class)
            ->let(['fullPath' => '$fullPath'])
            ->pipeline([
                [
                    '$match' => [
                        '$expr' => [
                            '$in' => ['$code', '$$fullPath']
                        ]
                    ]
                ],
                [
                    '$match' => [
                        'active' => false
                    ]
                ]
            ])
            ->alias('sections');

        $builder
            ->match()
            ->field('sections')
            ->equals([]);

        $builder->unwind('$' . $property);

        $builder
            ->addFields()
            ->field( $property . 'Code')
            ->expression('$' . $property . '.code');

        $builder
            ->group()
            ->field('_id')
            ->expression('$' . $property . 'Code');

        return $builder->getAggregation()->getIterator()->toArray();
    }

    public function findActiveSectionsByProductCode(string $productCode, ?string $sectionName, string $locale): array
    {
        $builder = $this->createAggregationBuilder();

        if (!empty($sectionName)) {
            $builder
                ->match()
                ->field('categoryName.name')
                ->equals(new Regex('(^|\s)' . $sectionName . '(\s|$)', 'i'));
        }

        $builder
            ->lookup(Product::class)
            ->localField('element.$id')
            ->foreignField('_id')
            ->alias('element');

        $builder
            ->match()
            ->field('element.code')
            ->equals($productCode);

        $builder
            ->match()
            ->field('section')
            ->notEqual(null)
            ->lookup(Section::class)
            ->localField('section.$id')
            ->foreignField('_id')
            ->alias('section')
            ->unwind('$section')
            ->match()
            ->field('section.active')
            ->equals(true)
            ->addFields()
            ->field('fullPath')
            ->expression([
                [
                    '$split' => [
                        [
                            '$cond' => [
                                ['$ifNull' => ['$section.path', false]],
                                ['$concat' => ['$section.path', ',', '$section.code']],
                                '$section.code'
                            ]
                        ],
                        ','
                    ]
                ]
            ])
            ->unwind('$fullPath')
            ->lookup(Section::class)
            ->let(['fullPath' => '$fullPath'])
            ->pipeline([
                [
                    '$match' => [
                        '$expr' => [
                            '$in' => ['$code', '$$fullPath'],
                        ],
                    ]
                ],
                [
                    '$match' => [
                        'active' => false,
                        'locale' => LocaleType::fromString($locale)->value
                    ]
                ]
            ])
            ->alias('parentSection')
            ->match()
            ->field('parentSection')
            ->equals([]);

        $builder
            ->addFields()
            ->field('sectionCode')
            ->expression('$section.code');

        $builder
            ->project()
            ->field('_id')
            ->expression(false)
            ->field('sectionCode')
            ->expression(true)
            ->field('fullPath')
            ->expression(true);

        $builder
            ->group()
            ->field('_id')
            ->expression([
                'sectionCode' => '$sectionCode',
                'fullPath' => '$fullPath',
            ]);

        return $builder->getAggregation()->getIterator()->toArray();
    }

    public function getSections(string $productCode, string $locale): array
    {
        $builder = $this->createAggregationBuilder();

        $builder
            ->lookup(Product::class)
            ->localField('element.$id')
            ->foreignField('_id')
            ->alias('product');

        $builder
            ->match()
            ->field('product.code')
            ->equals($productCode)
            ->field('product.active')
            ->equals(true);

        $builder
            ->addFields()
            ->field('filteredName')
            ->filter('$categoryName', 'name', $builder->expr()->eq('$$name.locale', $locale))
            ->unwind('$filteredName');

        $builder
            ->group()
            ->field('_id')
            ->expression('$filteredName.name');

        return $builder->getAggregation()->getIterator()->toArray();
    }
}