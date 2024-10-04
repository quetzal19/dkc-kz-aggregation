<?php

namespace App\Helper\Locator\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class EntityLoggerLocator
{
    public function __construct(
        #[Autowire(service: 'app.entity_logger_locator')]
        private ServiceLocator $locator,
    ) {
    }

    public function getLogger(string $entity): ?LoggerInterface
    {
        if (!$this->locator->has($entity)) {
            return null;
        }
        return $this->locator->get($entity);
    }
}