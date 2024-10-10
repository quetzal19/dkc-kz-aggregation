<?php

namespace App\Command\Cron;

use App\Features\TempStorage\Repository\TempStorageRepository;
use Symfony\Component\Console\{Attribute\AsCommand, Command\Command, Input\InputInterface, Output\OutputInterface};

#[AsCommand(
    name: 'cron:remove-temp-storage-with-error',
    description: 'Remove temp storage with error, after 2 days',
    aliases: ['cron:remove-temp-storage-with-error']
)]
final class RemoveTempStorageWithErrorCommand extends Command
{
    public function __construct(
        private readonly TempStorageRepository $storageRepository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->storageRepository->removeStoragesWithError();
        return Command::SUCCESS;
    }
}