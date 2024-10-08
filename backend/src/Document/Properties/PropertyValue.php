<?php

namespace App\Document\Properties;

use App\Document\Properties\Name\PropertyName;
use App\Features\Properties\PropertyValue\Repository\PropertyValueRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\Document(repositoryClass: PropertyValueRepository::class)]
class PropertyValue
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private ?string $id;

    #[MongoDB\Field(type: Type::STRING, nullable: true)]
    #[MongoDB\Index(keys: ['code' => 1], partialFilterExpression: [
        'code' => [
            ['$ne' => ''],
            ['$ne' => null]
        ]
    ])]
    private ?string $code;

    #[MongoDB\EmbedMany(targetDocument: PropertyName::class)]
    private Collection $names;

    public function __construct()
    {
        $this->names = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): PropertyValue
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): PropertyValue
    {
        $this->code = $code;
        return $this;
    }

    public function getCodeOrId(): ?string
    {
        return empty($this->code) ? $this->id : $this->code;
    }

    /** @return Collection<int, PropertyName> */
    public function getNames(): Collection
    {
        return $this->names;
    }

    public function addName(PropertyName $propertyName): PropertyValue
    {
        $this->names->add($propertyName);
        return $this;
    }

    public function setNames(Collection $names): PropertyValue
    {
        $this->names = $names;
        return $this;
    }
}