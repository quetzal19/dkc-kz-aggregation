<?php

namespace App\Features\Accessory\Handler;

use App\Features\Accessory\DTO\Message\AccessoryMessageDTO;
use App\Features\Accessory\Service\AccessoryActionService;
use App\Features\Message\Service\MessageService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;
use Psr\Log\LoggerInterface;

final  readonly class AccessoryHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY = 'accessory';

    public function __construct(
        AccessoryActionService $actionService,
        LoggerInterface $logger,
        MessageService $messageService,
    ) {
        parent::__construct($actionService, $logger, $messageService, AccessoryMessageDTO::class, self::ENTITY);
    }
}