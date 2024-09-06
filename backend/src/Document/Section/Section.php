<?php

namespace App\Document\Section;

use App\Helper\Enum\LocaleType;
use App\Patterns\TreeNode;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};
use App\Features\Section\Repository\SectionRepository;

#[MongoDB\Document(repositoryClass: SectionRepository::class)]
#[MongoDB\UniqueIndex(keys: ['code' => 'asc', 'local' => 'asc'])]
#[MongoDB\HasLifecycleCallbacks()]
class Section extends TreeNode
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private string $id;

    #[MongoDB\Field(type: Type::STRING)]
    private string $code;

    #[MongoDB\Field(type: Type::INT, enumType: LocaleType::class)]
    private LocaleType $locale;

    #[MongoDB\Field(type: Type::STRING, nullable: true)]
    private ?string $parentCode = null;

    #[MongoDB\Field(type: Type::STRING)]
    private string $name;

    #[MongoDB\Field(type: Type::BOOL)]
    private bool $active = true;

    #[MongoDB\Field(type: Type::INT)]
    private int $sort = 100;

    #[MongoDB\Field(type: Type::DATE)]
    private ?DateTimeInterface $updatedAt = null;

    #[MongoDB\Field(type: Type::STRING)]
    protected ?string $path = null;


    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Section
    {
        $this->code = $code;
        return $this;
    }

    public function getLocale(): LocaleType
    {
        return $this->locale;
    }

    public function setLocale(LocaleType $locale): Section
    {
        $this->locale = $locale;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Section
    {
        $this->name = $name;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Section
    {
        $this->active = $active;
        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): Section
    {
        $this->sort = $sort;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[MongoDB\PreUpdate()]
    #[MongoDB\PrePersist()]
    public function setUpdatedAt(): Section
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getParentCode(): ?string
    {
        return $this->parentCode;
    }

    public function setParent(?Section $parent): Section
    {
        $this->setParentNode($parent);

        $this->parentCode = $parent?->getCode();

        return $this;
    }
}
