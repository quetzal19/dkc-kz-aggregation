<?php

namespace App\Features\Properties\PropertyUnit\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Properties\PropertyUnit\DTO\Message\PropertyUnitMessageDTO;
use App\Features\Properties\PropertyUnit\Service\PropertyUnitActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;
use Psr\Log\LoggerInterface;

final readonly class PropertyUnitHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'etim_unit';

    public function __construct(
        PropertyUnitActionService $actionService,
        MessageService $messageService,
        LoggerInterface $logger
    ) {
        parent::__construct(
            $actionService,
            $logger,
            $messageService,
            PropertyUnitMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}