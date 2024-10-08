<?php

namespace App\Document\Accessory;

use App\Document\Category\Name\CategoryName;
use App\Document\Product\Product;
use App\Document\Section\Section;
use App\Features\Accessory\Repository\AccessoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\Index(keys: ['externalId' => 'asc'])]
#[MongoDB\Document(repositoryClass: AccessoryRepository::class)]
class Accessory
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private string $id;

    #[MongoDB\Field(type: Type::BOOL, nullable: true)]
    private ?bool $isDeleted = null;

    public function __construct(
        #[MongoDB\Field(type: Type::STRING)]
        private string $externalId,

        #[MongoDB\ReferenceOne(targetDocument: Product::class)]
        private ?Product $element = null,

        #[MongoDB\ReferenceOne(targetDocument: Product::class)]
        private ?Product $accessory = null,

        #[MongoDB\ReferenceOne(targetDocument: Section::class)]
        private ?Section $section = null,

        #[MongoDB\EmbedMany(targetDocument: CategoryName::class)]
        private Collection $categoryName = new ArrayCollection(),
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): Accessory
    {
        $this->id = $id;
        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): Accessory
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function getElement(): ?Product
    {
        return $this->element;
    }

    public function setElement(?Product $element): Accessory
    {
        $this->element = $element;
        return $this;
    }

    public function getAccessory(): ?Product
    {
        return $this->accessory;
    }

    public function setAccessory(?Product $accessory): Accessory
    {
        $this->accessory = $accessory;
        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): Accessory
    {
        $this->section = $section;
        return $this;
    }

    public function getCategoryName(): Collection
    {
        return $this->categoryName;
    }

    public function setCategoryName(Collection $categoryName): Accessory
    {
        $this->categoryName = $categoryName;
        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): Accessory
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

}