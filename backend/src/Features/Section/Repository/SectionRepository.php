<?php

namespace App\Features\Section\Repository;

use App\Document\Section\Section;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Exception;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

class SectionRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }

    /**
     * @throws MappingException
     * @throws Exception
     */
    public function findChildrenByCode(string $code, int $locale): array
    {
        $builder = $this->createAggregationBuilder();
        $builder
            ->match()
                ->field('code')
                ->equals($code)
            ->graphLookup(Section::class)
                ->startWith('$code')
                ->connectFromField('code')
                ->connectToField('parentCode')
                ->alias('children')
                ->maxDepth(10)
                ->depthField('depth')
            ->unwind('$children')
            ->match()
                ->field('children.locale')
                ->equals($locale)
            ->replaceRoot('$children');

        return $builder
            ->hydrate(Section::class)
            ->getAggregation()
            ->getIterator()
            ->toArray();
    }
}
