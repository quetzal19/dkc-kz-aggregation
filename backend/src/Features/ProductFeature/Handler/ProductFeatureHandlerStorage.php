<?php

namespace App\Features\ProductFeature\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\ProductFeature\DTO\Message\ProductFeatureMessageDTO;
use App\Features\ProductFeature\Service\ProductFeatureActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final readonly class ProductFeatureHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'product_features';

    public function __construct(
        ProductFeatureActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $messageService,
            ProductFeatureMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}