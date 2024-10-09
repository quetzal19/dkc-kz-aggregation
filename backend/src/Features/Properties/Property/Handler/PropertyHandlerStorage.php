<?php

namespace App\Features\Properties\Property\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Properties\Property\DTO\Message\PropertyMessageDTO;
use App\Features\Properties\Property\Service\PropertyActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final readonly class PropertyHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'etim_feature';

    public function __construct(
        PropertyActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $messageService,
            PropertyMessageDTO::class,
            self::ENTITY_NAME
        );
    }

}