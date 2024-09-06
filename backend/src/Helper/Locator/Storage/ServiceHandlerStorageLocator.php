<?php

namespace App\Helper\Locator\Storage;

use App\Helper\Interface\EntityHandlerStorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class ServiceHandlerStorageLocator
{
    public function __construct(
        #[Autowire(service: 'app.service_handler_storage_locator')]
        private ServiceLocator $locator,
    ) {
    }

    public function getHandler(string $entity): ?EntityHandlerStorageInterface
    {
        if (!$this->locator->has($entity)) {
            return null;
        }
        return $this->locator->get($entity);
    }

}