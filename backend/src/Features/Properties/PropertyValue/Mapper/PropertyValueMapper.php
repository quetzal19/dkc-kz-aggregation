<?php

namespace App\Features\Properties\PropertyValue\Mapper;

use App\Document\Properties\PropertyValue;
use App\Features\Properties\PropertyName\Mapper\PropertyNameMapper;
use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use App\Helper\Interface\Mapper\MapperMessageInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PropertyValueMapper implements MapperMessageInterface
{
    /** @param PropertyNameMapper $propertyNameMapper */
    public function __construct(
        #[Autowire(service: 'map.property.name.mapper')]
        private MapperMessageInterface $propertyNameMapper,
    ) {
    }

    /**
     * @param PropertyValue|null $entity
     * @param PropertyValueMessageDTO $dto
     * @return PropertyValue
     */
    public function mapFromMessageDTO(mixed $dto, mixed $entity = null): object
    {
        if (!$entity) {
            $entity = new PropertyValue();
        }

        $names = new ArrayCollection();
        foreach ($dto->names as $name) {
            $names->add(
                $this->propertyNameMapper->mapFromMessageDTO($name)
            );
        }

        return $entity
            ->setCode($dto->code)
            ->setNames($names->isEmpty() ? $entity->getNames() : $names);
    }
}