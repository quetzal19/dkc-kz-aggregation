<?php

namespace App\Command;

use App\Enum\Action;
use App\Enum\Entity;
use App\Message\BitrixImport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:section-import',
    description: 'Test section import',
    aliases: ['app:si'],
    hidden: false
)]
class SectionBitrixImportCommand extends Command
{

    public function __construct(protected MessageBusInterface $bus)
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        // Параметр колличества сообщений которые отправит команда
        $this->setHelp('This command import section data from file');
        $this->addArgument('count', InputArgument::OPTIONAL, 'Count of messages', 1);

    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $messageCount = $input->getArgument('count');
        // read data from file
        $data = json_decode(file_get_contents('/var/www/backend/data/b_iblock_section.json'), true, 512, JSON_THROW_ON_ERROR);

        foreach ($data as $item) {
            if ($messageCount-- <= 0) {
                break;
            }

            $message = new BitrixImport();

            $message->setTimestamp($item['timestamp']);
            $message->setEntity(Entity::from($item['entity']));
            $message->setAction(Action::from($item['action']));
            $message->setMessage(json_encode($item['message']));

            $this->bus->dispatch($message);
        }

        return 0;
    }
}
