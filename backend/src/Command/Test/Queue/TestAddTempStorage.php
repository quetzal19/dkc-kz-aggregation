<?php

namespace App\Command\Test\Queue;

use App\Features\TempStorage\DTO\Message\TempStorageMessage;
use App\Features\TempStorage\DTO\TempStorageDTO;
use Symfony\Component\Console\{
    Attribute\AsCommand,
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface,
};
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsCommand(
    name: 'test:queue:message-add',
    description: 'Добавляет сообщение в очередь',
    aliases: ['test:queue:message-add']
)]
class TestAddTempStorage extends Command
{

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument(
            name: 'message',
            mode: InputArgument::REQUIRED,
            description: 'Сообщение, строка в формате json, для добавления в очередь'
        );
    }

    /**
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $message = $this->serializer->deserialize(
                $input->getArgument('message'),
                TempStorageDTO::class,
                'json'
            );

            $this->messageBus->dispatch(new TempStorageMessage($message));
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
