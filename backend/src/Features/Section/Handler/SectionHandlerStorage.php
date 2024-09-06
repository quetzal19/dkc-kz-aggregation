<?php

namespace App\Features\Section\Handler;

use App\Document\Storage\Temp\TempStorage;
use App\Features\Section\Service\SectionActionService;
use App\Helper\Interface\EntityHandlerStorageInterface;
use Psr\Log\LoggerInterface;

final readonly class SectionHandlerStorage implements EntityHandlerStorageInterface
{
    public function __construct(
        private SectionActionService $actionService,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(TempStorage $storage): void
    {
        $action = $storage->getAction();

        try {
            $message = json_decode($storage->getMessage(), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->error(
                'Could not decode json: ' . $storage->getMessage() .
                ' exception: ' . $e->getMessage());
            return;
        }

        if (!method_exists($this->actionService, $action)) {
            $this->logger->error("Method '$action' not found, in section action service");
            return;
        }

        $this->actionService->$action($message);
    }
}