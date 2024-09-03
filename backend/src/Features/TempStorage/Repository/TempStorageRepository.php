<?php

namespace App\Features\TempStorage\Repository;

use App\Document\Storage\Temp\TempStorage;
use App\Helper\Enum\SortType;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

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
            ->sort('priority',  SortType::DESC->value)
            ->getQuery()
            ->execute();
    }
}