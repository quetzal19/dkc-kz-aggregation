<?php

namespace App\Command\Test\Queue;

use App\Features\TempStorage\DTO\TempStorageDTO;
use Symfony\Component\Console\{Attribute\AsCommand,
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface
};
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\{Exception\ExceptionInterface, MessageBusInterface};
use Symfony\Component\Serializer\SerializerInterface;

#[AsCommand(
    name: 'test:filling-queue-by-files',
    description: 'Test filling queue by files in data folder',
    aliases: ['test:filling-queue-by-files']
)]
final class TestFillingQueueByFiles extends Command
{
    private const DATA_FILE_EXTENSION = '.json';

    public function __construct(
        #[Autowire('%kernel.project_dir%/data/')]
        private readonly string $pathToData,
        private readonly SerializerInterface $serializer,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'number',
            InputArgument::OPTIONAL,
            'Number of messages to fill queue',
            1
        );
        $this->addArgument(
            'entity',
            InputArgument::OPTIONAL,
            'Entity to fill queue'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $numberMessages = $input->getArgument('number');
        $entity = $input->getArgument('entity');

        if (!is_numeric($numberMessages) || $numberMessages < 1) {
            $output->writeln('Number is not valid: ' . $numberMessages);
            return Command::FAILURE;
        }

        /** @var string[] $scanningFiles */
        $scanningFiles = scandir($this->pathToData);
        if (!$scanningFiles) {
            $output->writeln('Folder could not be read: ' . $this->pathToData);
            return Command::FAILURE;
        }

        $files = array_diff($scanningFiles, ['..', '.']);
        if (empty($files)) {
            $output->writeln('Folder is empty: ' . $this->pathToData);
            return Command::FAILURE;
        }

        $totalCountFillingMessages = 0;
        shuffle($files);

        foreach ($files as $file) {
            $fullPathToFile = $this->pathToData . $file;
            if (!is_file($fullPathToFile)) {
                continue;
            }
            if (!str_ends_with($file, self::DATA_FILE_EXTENSION)) {
                continue;
            }

            $jsonContent = file_get_contents($fullPathToFile);
            if (!$jsonContent) {
                $output->writeln('File is empty or invalid: ' . $file);
                continue;
            }

            try {
                $jsonDecoded = json_decode($jsonContent, true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $output->writeln('Could not decode json: ' . $file);
                $output->writeln('Error ' . $e->getMessage());
                continue;
            }

            shuffle($jsonDecoded);

            /** @var array<string, mixed> $jsonObject */
            foreach ($jsonDecoded as $jsonObject) {
                if ($totalCountFillingMessages >= $numberMessages) {
                    break 2;
                }

                if (!empty($entity) && array_key_exists('entity', $jsonObject) && $jsonObject['entity'] != $entity) {
                    continue;
                }

                /** @var TempStorageDTO $storageMessage */
                $storageMessage = $this->serializer->deserialize(
                    json_encode($jsonObject),
                    TempStorageDTO::class,
                    'json',
                );

                try {
                    $this->messageBus->dispatch($storageMessage);
                } catch (ExceptionInterface $e) {
                    $output->writeln("Error filling queue: " . $e->getMessage());
                    continue;
                }

                $totalCountFillingMessages++;
            }
        }

        if ($totalCountFillingMessages == 0) {
            $output->writeln(
                'Error filling queue, check if all the files are in the folder or if they are valid: ' . $this->pathToData
            );
            return Command::FAILURE;
        }

        $output->writeln('Total count filling messages: ' . $totalCountFillingMessages);

        return Command::SUCCESS;
    }
}