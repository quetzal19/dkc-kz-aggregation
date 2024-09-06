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
    public function findParentsByCode(string $code): array
    {
        $builder = $this->createAggregationBuilder();

        $builder
            ->match()
                ->field('code')
                ->equals($code)
            ->graphLookup('Section')
                ->startWith('$parentCode')
                ->connectFromField('parentCode')
                ->connectToField('code')
                ->alias('ancestors')
            ->unwind('$ancestors')
            ->replaceWith('$ancestors');

        return $builder
            ->hydrate(Section::class)
            ->getAggregation()
            ->getIterator()
            ->toArray();
    }
}
