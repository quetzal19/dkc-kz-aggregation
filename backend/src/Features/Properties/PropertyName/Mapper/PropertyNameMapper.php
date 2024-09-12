<?php

namespace App\Features\Properties\PropertyName\Mapper;

use App\Document\Properties\Name\PropertyName;
use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use App\Helper\Interface\Mapper\MapperMessageInterface;

final readonly class PropertyNameMapper implements MapperMessageInterface
{

    /**
     * @param PropertyNameMessageDTO $dto
     * @param PropertyName|null $entity
     * @return PropertyName
     */
    public function mapFromMessageDTO(mixed $dto, mixed $entity = null): object
    {

        if (!$entity) {
            $entity = new PropertyName();
        }

        return $entity
            ->setName($dto->name)
            ->setLocale($dto->locale);
    }
}