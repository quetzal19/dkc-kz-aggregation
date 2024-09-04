<?php

namespace App\Features\TempStorage\Repository;

use App\Document\Storage\Temp\TempStorage;
use App\Helper\Enum\SortType;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};
use Doctrine\ODM\MongoDB\{Aggregation\Builder, MongoDBException};

class TempStorageRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TempStorage::class);
    }

    /**
     * @throws MongoDBException
     */
    public function findHighPriority(): \Iterator
    {
        return $this->createQueryBuilder()
            ->sort('priority', SortType::DESC->value)
            ->getQuery()
            ->execute();
    }

    public function builderBasePipeline(): Builder
    {
        $builder = $this->createAggregationBuilder();

        $builder
            ->sort('timestamp', SortType::ASC->value)
            ->sort('actionPriority', SortType::DESC->value)
            ->sort('priority', SortType::DESC->value);

        return $builder;
    }
}