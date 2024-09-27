<?php

namespace App\Features\Properties\Property\Mapper;

use App\Document\Properties\Property;
use App\Features\Properties\Property\DTO\Message\PropertyMessageDTO;
use App\Features\Properties\PropertyName\Mapper\PropertyNameMapper;
use App\Helper\Interface\Mapper\MapperMessageInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PropertyMapper implements MapperMessageInterface
{
    /** @param PropertyNameMapper $propertyNameMapper */
    public function __construct(
        #[Autowire(service: 'map.property.name.mapper')]
        private MapperMessageInterface $propertyNameMapper,
    ) {
    }

    /**
     * @param Property|null $entity
     * @param PropertyMessageDTO $dto
     * @return Property
     */
    public function mapFromMessageDTO(mixed $dto, mixed $entity = null): object
    {
        if (!$entity) {
            $entity = new Property();
        }

        $names = new ArrayCollection();
        foreach ($dto->names as $name) {
            $names->add(
                $this->propertyNameMapper->mapFromMessageDTO($name)
            );
        }

        return $entity
            ->setCode($dto->code)
            ->setNames($names);
    }
}