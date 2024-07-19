<?php

namespace App\Command;

use App\Message\ProductImport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:product-import',
    description: 'Test product import',
    aliases: ['app:product-import'],
    hidden: false
)]
class TestProductImportCommand extends Command
{
    public function __construct(protected MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch((new ProductImport())->setMessage('{"name":"test_product","price":12345}'));

        return Command::SUCCESS;
    }
}
