<?php

namespace App\Features\ProductFeature\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\ProductFeature\DTO\Message\ProductFeatureMessageDTO;
use App\Features\ProductFeature\Service\ProductFeatureActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;
use Psr\Log\LoggerInterface;

final readonly class ProductFeatureHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'product_feature';

    public function __construct(
        ProductFeatureActionService $actionService,
        LoggerInterface $logger,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $logger,
            $messageService,
            ProductFeatureMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}