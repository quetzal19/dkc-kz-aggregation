<?php

namespace App\Document;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(repositoryClass: ProductRepository::class)]
class Section
{
    #[MongoDB\Id(type: "string", strategy: "UUID")]
    protected string $id;

    #[MongoDB\Field(type: "string")]
    protected string $code;

    #[MongoDB\EmbedMany(targetDocument: SectionLocale::class)]
    protected Collection $sectionLocales;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getSectionLocales(): Collection
    {
        return $this->sectionLocales;
    }

    public function setSectionLocales(Collection $sectionLocales): void
    {
        $this->sectionLocales = $sectionLocales;
    }
}
