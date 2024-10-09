<?php

namespace App\Features\Properties\PropertyFeatureMap\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\Properties\PropertyFeatureMap\DTO\Message\PropertyFeatureMapMessageDTO;
use App\Features\Properties\PropertyFeatureMap\Service\PropertyFeatureMapActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final readonly class PropertyFeatureMapHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'etim_art_class_feature_map';

    public function __construct(
        PropertyFeatureMapActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            actionService: $actionService,
            messageService: $messageService,
            dtoClass: PropertyFeatureMapMessageDTO::class,
            entity: self::ENTITY_NAME
        );
    }
}