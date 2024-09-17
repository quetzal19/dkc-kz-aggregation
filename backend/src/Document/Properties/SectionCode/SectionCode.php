<?php

namespace App\Document\Properties\SectionCode;

use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\EmbeddedDocument]
class SectionCode
{
    public function __construct(
        #[MongoDB\Field(type: Type::STRING)]
        private string $sectionCode,

        #[MongoDB\Field(type: Type::INT, nullable: true)]
        private ?int $sort,
    ) {
    }

    public function getSectionCode(): string
    {
        return $this->sectionCode;
    }

    public function setSectionCode(string $sectionCode): SectionCode
    {
        $this->sectionCode = $sectionCode;
        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): SectionCode
    {
        $this->sort = $sort;
        return $this;
    }


}