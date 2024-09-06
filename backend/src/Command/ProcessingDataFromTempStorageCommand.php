<?php

namespace App\Command;

use App\Features\Priority\{Builder\PriorityFilterBuilder, Service\PriorityService};
use App\Helper\Locator\Storage\ServiceHandlerStorageLocator;
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\{Attribute\AsCommand, Command\Command, Input\InputInterface, Output\OutputInterface};

#[AsCommand(
    name: 'processing:data-from-temp-storage',
    description: 'Processing data from temp storage',
    aliases: ['processing:data-from-temp-storage']
)]
final class ProcessingDataFromTempStorageCommand extends Command
{
    public function __construct(
        private readonly PriorityService $priorityService,
        private readonly ServiceHandlerStorageLocator $locator,
        private readonly LoggerInterface $logger,
        private readonly DocumentManager $documentManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filter = PriorityFilterBuilder::create()->build();

        try {
            $priorityData = $this->priorityService->getMaxPriorityData($filter);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return Command::FAILURE;
        }

        foreach ($priorityData as $storage) {
            $handler = $this->locator->getHandler($storage->getEntity());
            if (!$handler) {
                $this->logger->warning('Handler not found: ' . $storage->getEntity());
                $this->documentManager->remove($storage);
                continue;
            }
            $handler->handle($storage);
            $this->documentManager->remove($storage);
        }

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}