<?php

namespace App\Features\Product\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Product\DTO\Message\ProductMessageDTO;
use App\Features\Product\Service\ProductActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final readonly class ProductHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'b_iblock_element';

    public function __construct(
        ProductActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $messageService,
            ProductMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}