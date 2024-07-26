<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\EmbeddedDocument]
class SectionLocale
{
    #[MongoDB\Field(type: "string")]
    protected string $locale;

    #[MongoDB\Field(type: "string")]
    protected string $name;

    #[MongoDB\Field(type: "bool")]
    protected bool $active;

    #[MongoDB\Field(type: "string")]
    protected string $sort;

    #[MongoDB\Field(type: "string")]
    protected string $id;

    #[MongoDB\Field(type: "string")]
    protected string $parentId;

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getParentId(): string
    {
        return $this->parentId;
    }

    public function setParentId(string $parentId): void
    {
        $this->parentId = $parentId;
    }

}
