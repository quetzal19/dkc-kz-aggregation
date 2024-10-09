<?php

namespace App\Features\SectionFeature\Handler;

use App\Features\Message\Service\MessageService;
use App\Features\SectionFeature\DTO\Message\SectionFeatureMessageDTO;
use App\Features\SectionFeature\Service\SectionFeatureActionService;
use App\Helper\Abstract\AbstractEntityHandlerStorage;

final readonly class SectionFeatureHandlerStorage extends AbstractEntityHandlerStorage
{
    private const ENTITY_NAME = 'facet_feature_section';

    public function __construct(
        SectionFeatureActionService $actionService,
        MessageService $messageService,
    ) {
        parent::__construct(
            $actionService,
            $messageService,
            SectionFeatureMessageDTO::class,
            self::ENTITY_NAME
        );
    }
}