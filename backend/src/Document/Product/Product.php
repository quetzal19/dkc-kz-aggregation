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

    #[MongoDB\Field(type: Type::STRING, nullable: true )]
    private ?string $weight = '';

    #[MongoDB\Field(type: Type::STRING, nullable: true)]
    private ?string $volume = '';

    #[MongoDB\Field(type: Type::STRING, nullable: true)]
    private ?string $externalId = null;

    #[MongoDB\Field(type: Type::STRING, nullable: true)]
    private ?string $artClassId = null;

    /** @var string[] $property */
    #[MongoDB\Field(type: Type::COLLECTION)]
    private array $property = [];

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

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): Product
    {
        $this->weight = $weight;
        return $this;
    }

    public function getVolume(): ?string
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

    public function getArtClassId(): ?string
    {
        return $this->artClassId;
    }

    public function setArtClassId(?string $artClassId): Product
    {
        $this->artClassId = $artClassId;
        return $this;
    }

    public function getProperty(): array
    {
        return $this->property;
    }

    public function removePropertyByFeature(string $featureCode): void
    {
        if (empty($this->property)) {
            return;
        }

        foreach ($this->property as $index => $property) {
            [$feature] = explode(':', $property);
            if ($feature == $featureCode) {
                unset($this->property[$index]);
                break;
            }
        }
    }

    public function setProperties(?string $feature = null, ?string $value = null, ?string $unit = null): Product
    {
        $featureIsNull = is_null($feature);
        $valueIsNull = is_null($value);
        $unitIsNull = is_null($unit);

        if ($featureIsNull || ($valueIsNull && $unitIsNull)) {
            return $this;
        }

        if (empty($this->property)) {
            if (empty($value) && empty($unit)) {
                return $this;
            }
            $this->property[] = implode(':', [$feature, $value, $unit]);
            return $this;
        }

        $isFoundFeature = false;
        foreach ($this->property as $index => $property) {
            [$featureCode, $valueCode, $unitCode] = explode(':', $property);

            if ($featureCode == $feature) {
                $value = $valueIsNull ? $valueCode : $value;
                $unit = $unitIsNull ? $unitCode : $unit;

                if (empty($value) && empty($unit)) {
                    unset($this->property[$index]);
                    return $this;
                }

                $propertyString = implode(':', [$feature, $value, $unit]);
                $this->property[$index] = $propertyString;
                $isFoundFeature = true;
                break;
            }
        }

        if (!$isFoundFeature) {
            if (empty($value) && empty($unit)) {
                return $this;
            }
            $this->property[] = implode(':', [$feature, $value, $unit]);
        }

        return $this;
    }

    public function setProperty(array $property): Product
    {
        $this->property = $property;
        return $this;
    }

}