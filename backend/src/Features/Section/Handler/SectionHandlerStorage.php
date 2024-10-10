<?php

namespace App\Features\Section\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Section\DTO\Message\SectionMessageDTO;
use App\Features\Section\Service\SectionActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final readonly class SectionHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'b_iblock_section';

    public function __construct(
        SectionActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $messageService,
            SectionMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}