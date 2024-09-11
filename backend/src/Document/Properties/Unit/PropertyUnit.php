<?php

namespace App\Document\Properties\Unit;

use App\Document\Properties\Name\PropertyName;
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};
use Doctrine\Common\Collections\{ArrayCollection, Collection};

#[MongoDB\EmbeddedDocument]
class PropertyUnit
{
    #[MongoDB\Field(type: Type::STRING)]
    private ?string $code;
    #[MongoDB\EmbedMany(targetDocument: PropertyName::class)]
    private Collection $names;

    public function __construct()
    {
        $this->names = new ArrayCollection();
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): PropertyUnit
    {
        $this->code = $code;
        return $this;
    }

    public function getNames(): Collection
    {
        return $this->names;
    }

    public function addName(PropertyName $propertyName): PropertyUnit
    {
        $this->names->add($propertyName);
        return $this;
    }

    public function setNames(Collection $names): PropertyUnit
    {
        $this->names = $names;
        return $this;
    }


}