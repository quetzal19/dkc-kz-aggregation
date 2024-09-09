<?php

namespace App\Features\Product\Mapper;

use App\Document\Product\Product;
use App\Features\Product\DTO\Message\ProductMessageDTO;
use App\Helper\Enum\LocaleType;
use App\Helper\Interface\Mapper\MapperMessageInterface;

final readonly class ProductMapper implements MapperMessageInterface
{

    /**
     * @param ProductMessageDTO $dto
     * @param Product|null $entity
     * @return Product
     */
    public function mapFromMessageDTO(mixed $dto, mixed $entity = null): object
    {
        if (!$entity) {
            $entity = new Product();
        }

        return $entity
            ->setExternalId($dto->id)
            ->setCode($dto->code)
            ->setLocale(LocaleType::fromString($dto->locale))
            ->setActive($dto->active ?? $entity->isActive())
            ->setVolume($dto->volume ?? $entity->getVolume())
            ->setWeight($dto->weight ?? $entity->getWeight())
            ->setSort($dto->sort ?? $entity->getSort());
    }
}