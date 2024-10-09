<?php

namespace App\Features\Properties\PropertyValue\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use App\Features\Properties\PropertyValue\Service\PropertyValueActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final readonly class PropertyValueHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'etim_value';

    public function __construct(
        PropertyValueActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $messageService,
            PropertyValueMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}