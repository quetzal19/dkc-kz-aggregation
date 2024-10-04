<?php

namespace App\Features\Accessory\Handler;

use App\Features\Accessory\DTO\Message\AccessoryMessageDTO;
use App\Features\Accessory\Service\AccessoryActionService;
use App\Features\Message\Service\MessageService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final  readonly class AccessoryHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY = 'hl_accessories';

    public function __construct(
        AccessoryActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $messageService,
            AccessoryMessageDTO::class,
            self::ENTITY
        );
    }
}