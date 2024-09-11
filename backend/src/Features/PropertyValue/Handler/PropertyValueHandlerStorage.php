<?php

namespace App\Features\PropertyValue\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Property\Service\PropertyActionService;
use App\Features\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use App\Helper\Abstract\AbstractEntityHandlerStorage;
use Psr\Log\LoggerInterface;

final readonly class PropertyValueHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'etim_value';

    public function __construct(
        PropertyActionService $actionService,
        MessageService $messageService,
        LoggerInterface $logger
    ) {
        parent::__construct(
            $actionService,
            $logger,
            $messageService,
            PropertyValueMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}