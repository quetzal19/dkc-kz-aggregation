<?php

namespace App\Document\Properties;

use App\Document\Properties\{Name\PropertyName, SectionCode\SectionCode, Unit\PropertyUnit};
use App\Features\Properties\Property\Repository\PropertyRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\Document(repositoryClass: PropertyRepository::class)]
#[MongoDB\UniqueIndex(keys: ['code' => 'asc'])]
class Property
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private ?string $id;

    #[MongoDB\Field(type: Type::STRING)]
    private ?string $code;

    /** @var Collection<int, PropertyName> $names */
    #[MongoDB\EmbedMany(targetDocument: PropertyName::class)]
    private Collection $names;

    /** @var Collection<int, PropertyUnit> $units */
    #[MongoDB\EmbedMany(targetDocument: PropertyUnit::class)]
    private Collection $units;

    /** @var Collection<int, SectionCode> $units */
    #[MongoDB\EmbedMany(targetDocument: SectionCode::class)]
    private Collection $sectionCodes;

    public function __construct()
    {
        $this->names = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->sectionCodes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): Property
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): Property
    {
        $this->code = $code;
        return $this;
    }

    /** @return Collection<int, PropertyName> */
    public function getNames(): Collection
    {
        return $this->names;
    }

    public function addName(PropertyName $name): Property
    {
        $this->names->add($name);
        return $this;
    }

    public function setNames(Collection $names): Property
    {
        $this->names = $names;
        return $this;
    }

    /** @return Collection<int, PropertyUnit> */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(PropertyUnit $unit): Property
    {
        $this->units->add($unit);
        return $this;
    }

    /** @param null|Collection<int, PropertyName> $names */
    public function addOrUpdateUnit(string $code, ?Collection $names = null): void
    {
        $names ??= new ArrayCollection();

        if ($this->units->isEmpty()) {
            $this->units->add(
                (new PropertyUnit())
                    ->setCode($code)
                    ->setNames($names)
            );
        }

        foreach ($this->units as $unit) {
            if ($code == $unit->getCode()) {
                if ($names->isEmpty()) {
                    return;
                }

                $unit->setNames($names);
                return;
            }
        }

        $this->units->add(
            (new PropertyUnit())
                ->setCode($code)
                ->setNames($names)
        );
    }

    public function removeUnit(string $code): self
    {
        foreach ($this->units as $index => $unit) {
            if ($code == $unit->getCode()) {
                $this->units->remove($index);
            }
        }

        return $this;
    }

    public function setUnits(Collection $units): Property
    {
        $this->units = $units;
        return $this;
    }

    public function addOrUpdateSectionCode(SectionCode $sectionCode): self
    {
        foreach ($this->sectionCodes as $section) {
            if ($sectionCode->getSectionCode() == $section->getSectionCode()) {
                $section->setSort($sectionCode->getSort());
                return $this;
            }
        }

        $this->sectionCodes->add($sectionCode);

        return $this;
    }

    public function removeSectionCodeByCode(string $sectionCode): self
    {
        foreach ($this->sectionCodes as $index => $section) {
            if ($sectionCode == $section->getSectionCode()) {
                $this->sectionCodes->remove($index);
                return $this;
            }
        }
        return $this;
    }

    public function getSectionCodes(): Collection
    {
        return $this->sectionCodes;
    }

    public function setSectionCodes(Collection $sectionCodes): Property
    {
        $this->sectionCodes = $sectionCodes;
        return $this;
    }
}