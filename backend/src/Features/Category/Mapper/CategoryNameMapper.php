<?php

namespace App\Features\Category\Mapper;

use App\Document\Category\Name\CategoryName;
use App\Document\Properties\Name\PropertyName;
use App\Features\Category\DTO\Message\CategoryNameMessageDTO;
use App\Helper\Interface\Mapper\MapperMessageInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(id: 'map.category.name.mapper')]
final readonly class CategoryNameMapper implements MapperMessageInterface
{
    /**
     * @param CategoryNameMessageDTO $dto
     * @param CategoryName|null $entity
     * @return CategoryName
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