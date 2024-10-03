<?php

namespace App\Command;

use App\Features\TempStorage\Repository\TempStorageRepository;
use App\Features\Priority\{Builder\PriorityFilterBuilder, Service\PriorityService};
use App\Helper\Locator\Storage\ServiceHandlerStorageLocator;
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\{Attribute\AsCommand,
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface
};
use Symfony\Component\DependencyInjection\Attribute\Autowire;

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
        private readonly TempStorageRepository $storageRepository,

        #[Autowire('%app.entity_priorities%')]
        private readonly array $entityPriorities,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'limit',
            InputArgument::OPTIONAL,
            'Limit of messages to process',
            2500
        );
        $this->addArgument(
            'entity',
            InputArgument::OPTIONAL,
            'Entity to process',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = $input->getArgument('limit');
        if (!is_numeric($limit) || $limit < 1) {
            $output->writeln('Limit is not valid: ' . $limit);
            return Command::FAILURE;
        }

        $entities = $this->entityPriorities;
        arsort($entities);
        $entityNames = array_keys($entities);

        $entity = $input->getArgument('entity');

        if (!empty($entity)) {
            if (!in_array($entity, $entityNames)) {
                $output->writeln('Entity is not valid: ' . $entity);
                return Command::FAILURE;
            }

            $entityNames = [$entity];
        }


        foreach ($entityNames as $entity) {
            $handler = $this->locator->getHandler($entity);

            if (!$handler) {
                $this->logger->warning('Handler not found: ' . $entity);
                continue;
            }

            $filter = PriorityFilterBuilder::create()
                ->setEntity($entity)
                ->setPagination(1, $limit)
                ->build();

            try {
                $priorityData = $this->priorityService->getMaxPriorityData($filter);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            $storageIds = [];
            foreach ($priorityData as $storage) {
                if ($handler->handle($storage['message'], $storage['action'])) {
                    $storageIds[] = $storage['_id'];
                }
            }

            try {
                $this->documentManager->flush();
            } catch (MongoDBException $e) {
                $this->logger->error($e->getMessage());
            }

            try {
                $this->storageRepository->deleteByIds($storageIds);
            } catch (MongoDBException $e) {
                $this->logger->error($e->getMessage());
            }

            $this->documentManager->clear();
        }


        return Command::SUCCESS;
    }

}