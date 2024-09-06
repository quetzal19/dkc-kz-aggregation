<?php

namespace App\Command\Test;

use App\Features\Priority\Builder\PriorityFilterBuilder;
use App\Features\Priority\Service\PriorityService;
use Symfony\Component\Console\{Attribute\AsCommand, Command\Command, Input\InputInterface, Output\OutputInterface};

#[AsCommand(
    name: 'test:get-max-priority-data',
    description: 'Test get max priority data',
    aliases: ['test:get-max-priority-data']
)]
class TestGetMaxPriorityDataCommand extends Command
{
    public function __construct(
        private readonly PriorityService $priorityService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filterBuilder = PriorityFilterBuilder::create()
            ->setPagination(page: 1, limit: 10)
            ->setEntity(entity: 'etim_feature')
            ->setAction(action: 'update');

        $filter = $filterBuilder->build();
        try {
            $result = $this->priorityService->getMaxPriorityData($filter);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln($result);
        return Command::SUCCESS;
    }
}