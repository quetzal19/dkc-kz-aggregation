<?php

namespace App\Service\BitrixMessageHandler;

use App\Enum\Entity;
use LogicException;

class MessageHandlerFactory
{

    /**
     * При желании можно заменить типизацию всех аргументов на MessageHandlerInterface и определять их через конфиг
     * Но на данном этапе я не вижу необходимости в этом
     */
    public function __construct(
        protected SectionMessageHandler $sectionMessageHandler
    ) {
    }

    public function create(Entity $entity): MessageHandlerInterface
    {
        return match ($entity) {
            Entity::SECTION => $this->sectionMessageHandler,
            default => throw new LogicException('Message handler not found')
        };
    }
}
