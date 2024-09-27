<?php

namespace App\Features\Section\Mapper;

use App\Document\Section\Section;
use App\Features\Section\DTO\Message\SectionMessageDTO;
use App\Helper\Enum\LocaleType;
use App\Helper\Interface\Mapper\MapperMessageInterface;

class SectionMapper implements MapperMessageInterface
{
    /**
     * @param SectionMessageDTO $dto
     * @param Section|null $entity
     * @return Section
     */
    public function mapFromMessageDTO(mixed $dto, mixed $entity = null): object
    {
        $entity = $entity ?? new Section();
        return $entity
            ->setExternalId($dto->id)
            ->setSort($dto->sort ?? $entity->getSort())
            ->setActive($dto->active ?? $entity->isActive())
            ->setName($dto->name ?? $entity->getName())
            ->setLocale(LocaleType::fromString($dto->locale))
            ->setCode($dto->code);
    }
}