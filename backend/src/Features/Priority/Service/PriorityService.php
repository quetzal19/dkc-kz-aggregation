<?php

namespace App\Features\Priority\Service;

use App\Document\Storage\Temp\TempStorage;
use App\Features\Priority\Filter\PriorityFilter;
use App\Features\TempStorage\Repository\TempStorageRepository;
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

        private TempStorageRepository $storageRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getMaxPriorityData(PriorityFilter $priorityFilter): \Iterator
    {
        $builder = $this->storageRepository->builderBasePipeline($priorityFilter);

        $result = $builder
            ->project()
            ->field('message')
            ->expression('$message')
            ->field('action')
            ->expression('$action')
            ->getAggregation()
            ->getIterator();

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