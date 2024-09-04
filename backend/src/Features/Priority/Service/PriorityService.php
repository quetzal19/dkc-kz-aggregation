<?php

namespace App\Features\Priority\Service;

use App\Document\Storage\Temp\TempStorage;
use App\Features\Priority\Filter\PriorityFilter;
use Doctrine\ODM\MongoDB\{Aggregation\Builder, DocumentManager};
use Exception;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PriorityService
{
    public function __construct(
        #[Autowire('%app.action_priorities%')]
        private array $actionPriorities,

        #[Autowire('%app.entity_priorities%')]
        private array $entityPriorities,

        private DocumentManager $documentManager,
    ) {
    }

    /**
     * @return TempStorage[]
     * @throws Exception
     */
    public function getMaxPriorityData(PriorityFilter $priorityFilter): array
    {
        /** @var Builder $builder */
        $builder = $this->documentManager
            ->getRepository(TempStorage::class)
            ->builderBasePipeline();

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

        $result = $builder
            ->hydrate(TempStorage::class)
            ->getAggregation()
            ->getIterator()
            ->toArray();

        return $result;
    }

    /**
     * @throws LogicException
     */
    public function getPriorityByEntity(string $entity): int
    {
        if (!array_key_exists($entity, $this->entityPriorities)) {
            throw new LogicException('Unknown entity: ' . $entity);
        }
        return $this->entityPriorities[$entity];
    }

    /**
     * @throws LogicException
     */
    public function getPriorityByAction(string $action): int
    {
        if (!array_key_exists($action, $this->actionPriorities)) {
            throw new LogicException('Unknown action: ' . $action);
        }
        return $this->actionPriorities[$action];
    }
}