<?php

namespace App\Command;

use App\Document\Storage\Temp\{Error\ErrorMessage, TempStorage};
use App\Features\TempStorage\Error\Type\ErrorType;
use App\Features\TempStorage\Repository\TempStorageRepository;
use App\Helper\Locator\Logger\EntityLoggerLocator;
use App\Features\Priority\{Builder\PriorityFilterBuilder, Service\PriorityService};
use App\Helper\Locator\Storage\ServiceHandlerStorageLocator;
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use MongoDB\Driver\Exception\BulkWriteException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\{Attribute\AsCommand,
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface,
    Style\SymfonyStyle
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
        private readonly EntityLoggerLocator $loggerLocator,
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
            'entities',
            InputArgument::IS_ARRAY,
            'Entities to process',
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

        $entities = $input->getArgument('entities');

        if (!empty($entities)) {
            foreach ($entities as $entity) {
                if (!in_array($entity, $entityNames)) {
                    $output->writeln('Entity is not valid: ' . $entity);
                    return Command::FAILURE;
                }
            }

            $entityNames = $entities;
        }

        $io = new SymfonyStyle($input, $output);
        $io->info("before processing memory usage: " . $this->getCurrentMemoryUsage());

        foreach ($entityNames as $entity) {
            $io->title("START PROCESSING ENTITY: " . mb_strtoupper($entity) . ", LIMIT: $limit");
            $io->writeln(" <info>memory usage: " . $this->getCurrentMemoryUsage() . "</info>");

            $handler = $this->locator->getHandler($entity);
            $entityLogger = $this->loggerLocator->getLogger($entity);

            if (!$handler) {
                $this->logger->warning('Handler not found: ' . $entity);
                continue;
            }

            if (!$entityLogger) {
                $this->logger->warning('Logger not found for entity: ' . $entity);
                continue;
            }

            $filter = PriorityFilterBuilder::create()
                ->setEntity($entity)
                ->setPagination(1, $limit)
                ->build();

            try {
                $io->info("Fetching max priority data");
                $io->writeln(" <info>memory usage: " . $this->getCurrentMemoryUsage() . "</info>");

                $priorityData = $this->priorityService->getMaxPriorityData($filter);

                $io->info("Data successfully fetched");
                $io->writeln(" <info>memory usage: " . $this->getCurrentMemoryUsage() . "</info>");
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            $io->info("Start handle data");
            $io->writeln(" <info>memory usage: " . $this->getCurrentMemoryUsage() . "</info>");

            [$storageIds, $storageErrors] = [[], []];
            [$totalCount, $successfullyCount] = [0, 0];
            foreach ($priorityData as $storage) {
                $errorMessage = $handler->handle($storage['message'], $storage['action']);
                if ($errorMessage) {
                    $entityLogger->error($errorMessage->message);

                    if (in_array($errorMessage->errorType, ErrorType::getTypesForRemoveStorage())) {
                        $storageIds[] = $storage['_id'];
                    } else {
                        $storageErrors[$storage['_id']] = $errorMessage;
                    }
                } else {
                    $storageIds[] = $storage['_id'];
                    $successfullyCount++;
                }

                $totalCount++;
            }

            $io->info("Handled entity, totalCount: $totalCount successfully: $successfullyCount");
            $io->writeln(" <info>memory usage: " . $this->getCurrentMemoryUsage() . "</info>");

            try {
                $this->documentManager->flush();
                $io->info("Entities flushed");
            } catch (MongoDBException $e) {
                $this->logger->error($e->getMessage());
                $io->error("Error flush exception: " . $e->getMessage());
            } catch (BulkWriteException $e) {
                $entityLogger->error($e->getMessage());

                return Command::FAILURE;
            }

            $io->writeln(" <info>memory usage: " . $this->getCurrentMemoryUsage() . "</info>");

            foreach ($storageErrors as $storageId => $error) {
                /**
                 * @var TempStorage $storage
                 * @var ErrorMessage $error
                 */
                $storage = $this->storageRepository->find($storageId);
                if (is_null($storage->getErrorDate())) {
                    $storage->setErrorDate(new \DateTime());
                }
                $storage->setErrorMessage($error);
            }

            try {
                $this->storageRepository->deleteByIds($storageIds);
            } catch (MongoDBException $e) {
                $this->logger->error($e->getMessage());
            }

            try {
                $this->documentManager->flush();
                $io->info("Flushed deleted tempStorage");
            } catch (MongoDBException $e) {
                $this->logger->error($e->getMessage());
                $io->error("Error flush exception: " . $e->getMessage());
            }

            $io->writeln(" <info>memory usage: " . $this->getCurrentMemoryUsage() . "</info>");

            $this->documentManager->clear();

            $io->info("Entity processing finished");
            $io->writeln(" <info>memory usage: " . $this->getCurrentMemoryUsage() . "</info>");
        }


        return Command::SUCCESS;
    }

    private function getCurrentMemoryUsage(): string
    {
        return round(memory_get_usage() / 1_048_576.2, 1) . " MB";
    }
}