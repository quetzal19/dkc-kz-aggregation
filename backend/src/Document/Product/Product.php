<?php

namespace App\Document\Product;

use App\Document\Section\Section;
use App\Features\Product\Repository\ProductRepository;
use App\Helper\Enum\LocaleType;
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\Document(repositoryClass: ProductRepository::class)]
#[MongoDB\UniqueIndex(keys: ['code' => 'asc', 'locale' => 'asc'])]
class Product
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private string $id;

    #[MongoDB\Field(type: Type::STRING)]
    private string $code;

    #[MongoDB\Field(type: Type::INT, enumType: LocaleType::class)]
    private LocaleType $locale;

    #[MongoDB\ReferenceOne(targetDocument: Section::class)]
    private Section $section;

    #[MongoDB\Field(type: Type::BOOL)]
    private bool $active;

    #[MongoDB\Field(type: Type::INT)]
    private int $sort;

    #[MongoDB\Field(type: Type::STRING)]
    private string $weight;

    #[MongoDB\Field(type: Type::STRING)]
    private string $volume;

    #[MongoDB\Field(type: Type::STRING, nullable: true)]
    private ?string $externalId = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): Product
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Product
    {
        $this->code = $code;
        return $this;
    }

    public function getLocale(): LocaleType
    {
        return $this->locale;
    }

    public function setLocale(LocaleType $locale): Product
    {
        $this->locale = $locale;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Product
    {
        $this->active = $active;
        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): Product
    {
        $this->sort = $sort;
        return $this;
    }

    public function getWeight(): string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): Product
    {
        $this->weight = $weight;
        return $this;
    }

    public function getVolume(): string
    {
        return $this->volume;
    }

    public function setVolume(string $volume): Product
    {
        $this->volume = $volume;
        return $this;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function setSection(Section $section): Product
    {
        $this->section = $section;
        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Product
    {
        $this->externalId = $externalId;
        return $this;
    }

}