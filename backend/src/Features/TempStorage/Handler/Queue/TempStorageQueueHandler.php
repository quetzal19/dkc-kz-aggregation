<?php

namespace App\Features\TempStorage\Handler\Queue;

use App\Helper\Interface\MapperInterface;
use App\Features\TempStorage\{DTO\TempStorageDTO,
    Mapper\TempStorageMapper,
    Service\TempStorageService,
    Service\TempStorageValidatorService
};
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class TempStorageQueueHandler
{

    /** @param TempStorageMapper $tempStorageMapper */
    public function __construct(
        private MapperInterface $tempStorageMapper,
        private TempStorageService $service,
        private TempStorageValidatorService $validatorService,
    ) {
    }

    public function __invoke(TempStorageDTO $storageDTO): void
    {
        $this->validatorService->validateDTO($storageDTO);

        $storage = $this->tempStorageMapper->mapFromDTO($storageDTO);

        $this->service->save($storage);
    }

}