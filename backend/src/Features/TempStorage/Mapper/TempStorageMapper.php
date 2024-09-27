<?php

namespace App\Features\TempStorage\Mapper;

use App\Document\Storage\Temp\TempStorage;
use App\Features\Priority\Service\PriorityService;
use App\Features\TempStorage\DTO\TempStorageDTO;
use App\Helper\Interface\Mapper\MapperInterface;
use LogicException;

readonly class TempStorageMapper implements MapperInterface
{
    public function __construct(
        private PriorityService $priorityService,
    ) {
    }

    /**
     * @param TempStorageDTO $dto
     * @throws LogicException
     */
    public function mapFromDTO(mixed $dto): TempStorage
    {
        $message = $dto->message;
        if (is_array($message)) {
            $message = json_encode($message);
        }
        return new TempStorage(
            timestamp: $dto->timestamp,
            entity: $dto->entity,
            action: $dto->action,
            actionPriority: $this->priorityService->getPriorityByAction($dto->action),
            priority: $this->priorityService->getPriorityByEntity($dto->entity),
            message: $message,
        );
    }
}