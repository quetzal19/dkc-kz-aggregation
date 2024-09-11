<?php

namespace App\Document\Properties;

use App\Document\Properties\{Name\PropertyName, Unit\PropertyUnit};
use App\Features\Property\Repository\PropertyRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\Document(repositoryClass: PropertyRepository::class)]
#[MongoDB\UniqueIndex(keys: ['code' => 'asc'])]
class Property
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private ?string $id;

    #[MongoDB\Field(type: Type::STRING)]
    private ?string $code;

    #[MongoDB\EmbedMany(targetDocument: PropertyName::class)]
    private Collection $names;

    #[MongoDB\EmbedMany(targetDocument: PropertyUnit::class)]
    private Collection $units;

    public function __construct()
    {
        $this->names = new ArrayCollection();
        $this->units = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): Property
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): Property
    {
        $this->code = $code;
        return $this;
    }

    /** @return Collection<int, PropertyName> */
    public function getNames(): Collection
    {
        return $this->names;
    }

    public function addName(PropertyName $name): Property
    {
        $this->names->add($name);
        return $this;
    }

    public function setNames(Collection $names): Property
    {
        $this->names = $names;
        return $this;
    }

    /** @return Collection<int, PropertyUnit> */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(PropertyUnit $unit): Property
    {
        $this->units->add($unit);
        return $this;
    }

    public function setUnits(Collection $units): Property
    {
        $this->units = $units;
        return $this;
    }
}