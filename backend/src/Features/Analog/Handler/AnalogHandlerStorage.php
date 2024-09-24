<?php

namespace App\Features\Analog\Handler;

use App\Features\Analog\DTO\Message\AnalogMessageDTO;
use App\Features\Analog\Service\AnalogActionService;
use App\Features\Message\Service\MessageService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;
use Psr\Log\LoggerInterface;

final readonly class AnalogHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY = 'analog';
    public function __construct(
        AnalogActionService $actionService,
        LoggerInterface $logger,
        MessageService $messageService,
    ) {
        parent::__construct($actionService, $logger, $messageService, AnalogMessageDTO::class, self::ENTITY);
    }
}