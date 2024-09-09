<?php

namespace App\Features\Product\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Product\DTO\Message\ProductMessageDTO;
use App\Features\Product\Service\ProductActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;
use Psr\Log\LoggerInterface;

final readonly class ProductHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'product';

    public function __construct(
        ProductActionService $actionService,
        MessageService $messageService,
        LoggerInterface $logger
    ) {
        parent::__construct(
            $actionService,
            $logger,
            $messageService,
            ProductMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}