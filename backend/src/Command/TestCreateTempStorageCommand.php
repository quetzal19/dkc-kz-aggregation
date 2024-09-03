<?php

namespace App\Command;

use App\Document\Storage\Temp\TempStorage;
use App\Features\TempStorage\Service\TempStorageService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'test:create-temp-storage',
    description: 'Test create temp storage',
    aliases: ['test:create-temp-storage']
)]
class TestCreateTempStorageCommand extends Command
{
    public function __construct(
        private readonly TempStorageService $tempStorageService,
        private readonly DocumentManager $documentManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        [$timeStamp, $entity, $action, $priority] = ['1721900360', 'etim_feature', 'update', 1];

        $testStorage = new TempStorage(
            timestamp: $timeStamp,
            entity: $entity,
            action: $action,
            priority: $priority,
            message: '{}'
        );

        try {
            $id = $this->tempStorageService->save($testStorage);
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
        $output->writeln('Test save temp storage: ' . $id);

        try {
            $tempStorage = $this->documentManager
                ->getRepository(TempStorage::class)
                ->find($id);
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
        $output->writeln('Test find by id temp storage: ' . $tempStorage->getId());

        try {
            /** @var \Iterator $highPriorityStorages */
            $highPriorityStorages = $this->documentManager
                ->getRepository(TempStorage::class)
                ->findHighPriority();
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $storagesId = array_map(
            fn(TempStorage $storage): string => $storage->getId(),
            iterator_to_array($highPriorityStorages)
        );
        $output->writeln('Test get temp storages: ' . join(', ', $storagesId));


        return Command::SUCCESS;
    }
}