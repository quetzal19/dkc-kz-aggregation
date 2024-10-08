<?php

declare(strict_types=1);

namespace App\Command\Cron;

use App\Features\Accessory\Repository\AccessoryRepository;
use App\Features\Analog\Repository\AnalogRepository;
use Symfony\Component\Console\{Attribute\AsCommand, Command\Command, Input\InputInterface, Output\OutputInterface};

#[AsCommand(name: 'processing:remove_marked_deleted_entities', description: 'Remove marked deleted entities.')]
class RemoveMarkedDeletedEntitiesCommand extends Command
{
    public function __construct(
        private readonly AnalogRepository $analogRepository,
        private readonly AccessoryRepository $accessoryRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->accessoryRepository->deleteMarked();
        $this->analogRepository->deleteMarked();
        return Command::SUCCESS;
    }
}
