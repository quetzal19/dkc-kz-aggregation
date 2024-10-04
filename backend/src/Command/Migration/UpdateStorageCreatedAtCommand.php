<?php

namespace App\Command\Migration;

use App\Features\TempStorage\Repository\TempStorageRepository;
use Symfony\Component\Console\{Attribute\AsCommand, Command\Command, Input\InputInterface, Output\OutputInterface};

#[AsCommand(
    name: 'migration:update-storage-created-at',
    description: 'Update storage created at',
    aliases: ['migration:update-storage-created-at']
)]
final class UpdateStorageCreatedAtCommand extends Command
{
    public function __construct(
        private readonly TempStorageRepository $storageRepository
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->storageRepository->updateStorageCreatedAt();
        return Command::SUCCESS;
    }
}