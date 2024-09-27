<?php

namespace App\Document\Analog;

use App\Document\Category\Name\CategoryName;
use App\Document\Product\Product;
use App\Document\Section\Section;
use App\Features\Analog\Repository\AnalogRepository;
use Doctrine\Common\{Collections\ArrayCollection, Collections\Collection};
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\Document(repositoryClass: AnalogRepository::class)]
class Analog
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private string $id;

    public function __construct(
        #[MongoDB\Field(type: Type::STRING)]
        private string $externalId,

        #[MongoDB\ReferenceOne(targetDocument: Product::class)]
        private Product $element,

        #[MongoDB\ReferenceOne(targetDocument: Product::class)]
        private ?Product $analog = null,

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

    public function setId(string $id): Analog
    {
        $this->id = $id;
        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): Analog
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function getElement(): Product
    {
        return $this->element;
    }

    public function setElement(Product $element): Analog
    {
        $this->element = $element;
        return $this;
    }

    public function getAnalog(): ?Product
    {
        return $this->analog;
    }

    public function setAnalog(?Product $analog): Analog
    {
        $this->analog = $analog;
        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): Analog
    {
        $this->section = $section;
        return $this;
    }

    public function getCategoryName(): Collection
    {
        return $this->categoryName;
    }

    public function setCategoryName(Collection $categoryName): Analog
    {
        $this->categoryName = $categoryName;
        return $this;
    }


}