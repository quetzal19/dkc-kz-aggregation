<?php

namespace App\Features\Analog\Handler;

use App\Features\Analog\DTO\Message\AnalogMessageDTO;
use App\Features\Analog\Service\AnalogActionService;
use App\Features\Message\Service\MessageService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final readonly class AnalogHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY = 'hl_analogs';

    public function __construct(
        AnalogActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $messageService,
            AnalogMessageDTO::class,
            self::ENTITY
        );
    }
}