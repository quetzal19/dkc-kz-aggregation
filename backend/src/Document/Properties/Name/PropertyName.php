<?php

namespace App\Document\Properties\Name;

use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\EmbeddedDocument]
class PropertyName
{
    #[MongoDB\Field(type: Type::STRING)]
    private string $name;
    #[MongoDB\Field(type: Type::STRING)]
    private string $locale;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): PropertyName
    {
        $this->name = $name;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): PropertyName
    {
        $this->locale = $locale;
        return $this;
    }


}