<?php

namespace App\Helper\Abstract;

use App\Helper\Interface\{ActionInterface, EntityHandlerStorageInterface, Storage\StorageInterface};
use App\Features\Message\Service\MessageService;
use Psr\Log\LoggerInterface;

abstract readonly class AbstractEntityHandlerStorage implements EntityHandlerStorageInterface
{
    public function __construct(
        private ActionInterface $actionService,
        private LoggerInterface $logger,
        private MessageService $messageService,
        private string $dtoClass,
        private string $entity,
    ) {
    }

    public function handle(string $message, string $action): bool
    {
        try {
            $message = json_decode($message, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->error(
                'Could not decode json: ' . $message .
                ' exception: ' . $e->getMessage()
            );
            return false;
        }

        if (!method_exists($this->actionService, $action)) {
            $this->logger->error("Method '$action' not found, in class " . get_class($this->actionService));
            return false;
        }

        $dto = $this->messageService->serializeToDTOAndValidate($message, [$action], $this->dtoClass, $this->entity);
        if (!$dto) {
            return false;
        }

        return $this->actionService->$action($dto);
    }
}