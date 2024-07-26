<?php

namespace App\MessageHandler;

use App\Message\BitrixImport;
use App\Service\BitrixMessageHandler\MessageHandlerFactory;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BitrixImportHandler
{
    public function __construct(
        protected LoggerInterface $bitrixImportLogger,
        protected MessageHandlerFactory $messageHandlerFactory
    ) {
    }

    public function __invoke(BitrixImport $message): void
    {
        try {
            $handler = $this->messageHandlerFactory->create($message->getEntity());
            $handler->handleMessage($message);

            $this->bitrixImportLogger->info(sprintf('Imported %s', $message->getEntity()->value));
        } catch (Exception $exception) {
            $this->bitrixImportLogger->error($exception->getMessage());
        }
    }
}
