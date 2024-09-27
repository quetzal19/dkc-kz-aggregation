<?php

namespace App\Features\TempStorage\Repository;

use App\Document\Storage\Temp\TempStorage;
use App\Features\Priority\Filter\PriorityFilter;
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

    public function builderBasePipeline(PriorityFilter $priorityFilter): Builder
    {
        $builder = $this->createAggregationBuilder();

        $builder
            ->sort('timestamp', SortType::ASC->value)
            ->sort('actionPriority', SortType::DESC->value)
            ->sort('priority', SortType::DESC->value);

        if ($paginationDTO = $priorityFilter->paginationDTO) {
            $builder
                ->limit($paginationDTO->getLimit())
                ->skip($paginationDTO->getSkip());
        }

        if ($priorityFilter->entity) {
            $builder
                ->match()
                ->field('entity')
                ->equals($priorityFilter->entity);
        }

        if ($priorityFilter->action) {
            $builder
                ->match()
                ->field('action')
                ->equals($priorityFilter->action);
        }

        if ($priorityFilter->priority) {
            $builder
                ->match()
                ->field('priority')
                ->equals($priorityFilter->priority);
        }

        if ($priorityFilter->actionPriority) {
            $builder
                ->match()
                ->field('actionPriority')
                ->equals($priorityFilter->actionPriority);
        }

        return $builder;
    }
}