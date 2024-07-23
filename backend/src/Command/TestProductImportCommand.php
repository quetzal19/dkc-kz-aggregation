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
        $data = [
            'code' => 'DFF3246435',
            'sectionCode' => 'FGH457964',
            'name' => 'test_product',
            'filters' => [
                [
                    'code' => 'GKD6844',
                    'value' => 'FJLD4056345',
                    'unit' => 'FGFH435345',

                ],
                [
                    'code' => 'FGH457964',
                    'value' => 'FGH457964',
                    'unit' => 'FGH457964',
                ],
                [
                    'code' => 'GKD6844',
                    'value' => 'GKD6844',
                    'unit' => 'GKD6844',
                ]
            ],
        ];

        $this->bus->dispatch((new ProductImport())->setMessage(json_encode($data)));

        return Command::SUCCESS;
    }
}
