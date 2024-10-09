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

    public function removeStoragesWithError(): void
    {
        $this->createQueryBuilder()
            ->remove()
                ->field('errorMessage')->notEqual(null)
                ->field('errorDate')->lte(new \DateTime('-2 days'))
            ->getQuery()
            ->execute();
    }

    /**
     * @throws MongoDBException
     */
    public function deleteByIds(array $ids): void
    {
        $this->createQueryBuilder()
            ->remove()
                ->field('_id')
                ->in($ids)
            ->getQuery()
            ->execute();
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

        if ($priorityFilter->entity) {
            $builder
                ->match()
                ->field('entity')
                ->equals($priorityFilter->entity);
        }

        $builder
            ->sort([
                'errorMessage' => SortType::ASC->value,
                'timestamp' => SortType::ASC->value,
                '_id' => SortType::ASC->value
            ]);

        if ($paginationDTO = $priorityFilter->paginationDTO) {
            $builder
                ->limit($paginationDTO->getLimit())
                ->skip($paginationDTO->getSkip());
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