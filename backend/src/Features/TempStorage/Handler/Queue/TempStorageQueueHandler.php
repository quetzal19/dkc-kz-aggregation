<?php

namespace App\Features\TempStorage\Handler\Queue;

use App\Features\TempStorage\{DTO\Message\TempStorageMessage,
    DTO\TempStorageDTO,
    Mapper\TempStorageMapper,
    Service\TempStorageService,
    Service\TempStorageValidatorService
};
use App\Helper\Interface\Mapper\MapperInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsMessageHandler]
final readonly class TempStorageQueueHandler
{

    /** @param TempStorageMapper $tempStorageMapper */
    public function __construct(
        private MapperInterface $tempStorageMapper,
        private TempStorageService $service,
        private TempStorageValidatorService $validatorService,
        private DenormalizerInterface $denormalizer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(TempStorageMessage $storageMessage): void
    {
        $this->logger->info('Message processing has started, message: ' . json_encode($storageMessage->data));

        $storageDTO = $this->denormalizer->denormalize(
            $storageMessage->data,
            TempStorageDTO::class,
            'json'
        );

        try {
            $this->validatorService->validateDTO($storageDTO);
        } catch (ValidationFailedException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $storage = $this->tempStorageMapper->mapFromDTO($storageDTO);

        $this->service->save($storage);

        $this->logger->info('Message processed');
    }

}