<?php

namespace App\Document\Category\Name;

use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\EmbeddedDocument]
class CategoryName
{
    #[MongoDB\Field(type: Type::STRING, nullable: true)]
    private string $name;
    #[MongoDB\Field(type: Type::STRING)]
    private string $locale;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }
}