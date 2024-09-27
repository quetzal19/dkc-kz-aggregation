<?php

namespace App\Features\Section\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Section\DTO\Message\SectionMessageDTO;
use App\Features\Section\Service\SectionActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;
use Psr\Log\LoggerInterface;

final readonly class SectionHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'section';

    public function __construct(
        SectionActionService $actionService,
        MessageService $messageService,
        LoggerInterface $logger
    ) {
        parent::__construct($actionService, $logger, $messageService, SectionMessageDTO::class, self::ENTITY_NAME);
    }
}