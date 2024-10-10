<?php

namespace App\Helper\Abstract;

use App\Helper\Interface\{ActionInterface, EntityHandlerStorageInterface};
use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\Message\Service\MessageService;
use App\Features\TempStorage\Error\Type\ErrorType;
use App\Helper\Abstract\Error\AbstractErrorMessage;

abstract readonly class AbstractEntityHandlerStorage implements EntityHandlerStorageInterface
{
    public function __construct(
        private ActionInterface $actionService,
        private MessageService $messageService,
        private string $dtoClass,
        private string $entity,
    ) {
    }

    public function handle(string $message, string $action): ?AbstractErrorMessage
    {
        try {
            $message = json_decode($message, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return new ErrorMessage(
                ErrorType::VALIDATION_ERROR,
                'Could not decode json: ' . $message .
                ' exception: ' . $e->getMessage()
            );
        }

        if (!method_exists($this->actionService, $action)) {
            return new ErrorMessage(
                ErrorType::UNKNOWN_ERROR,
                "Method '$action' not found, in class " . get_class($this->actionService)
            );
        }

        try {
            $dto = $this->messageService->serializeToDTOAndValidate($message, [$action], $this->dtoClass);
        } catch (\Exception $e) {
            return new ErrorMessage(
                ErrorType::VALIDATION_ERROR,
                'Validation "' . $this->entity . '"  failed for group "' . $action . '": ' .
                $e->getMessage() . ", message: " . json_encode($message)
            );
        }

        return $this->actionService->$action($dto);
    }
}