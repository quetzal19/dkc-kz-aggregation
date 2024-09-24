<?php

namespace App\Features\Properties\Property\Repository;

use App\Document\Properties\Property;
use App\Helper\Enum\SortType;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

class PropertyRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @return Property[]
     * @throws \Exception
     */
    public function findBySectionCodes(array $codes): array
    {
        $builder = $this->createAggregationBuilder();

        $builder
            ->unwind('$sectionCodes')
            ->match()
            ->field('sectionCodes.sectionCode')
            ->in($codes)
            ->sort('sectionCodes.sort', SortType::DESC->value);

        return $builder
            ->hydrate(Property::class)
            ->getAggregation()
            ->getIterator()
            ->toArray();
    }
}