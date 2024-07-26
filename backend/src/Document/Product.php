<?php

namespace App\Document;

use App\Dto\Product\ProductDto;
use App\Dto\Product\ProductFilterInterface;
use App\Repository\ProductRepository;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(repositoryClass: ProductRepository::class)]
class Product
{
    #[MongoDB\Id(type: "string", strategy: "UUID")]
    protected string $id;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Не задан символьный код товара")]
    protected string $code;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Не задан символьный код категории товара")]
    protected string $sectionCode;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Не задано название товара")]
    protected string $name;

    #[MongoDB\Field(type: "string")]
    protected string $weight;

    #[MongoDB\Field(type: "string")]
    protected string $volume;

    #[MongoDB\Field(type: "collection")]
    #[Assert\All(
        constraints: [
            new Assert\Type(type: 'string'),
            new Assert\Regex(
                pattern: '/^[^:]+:[^:]+:[^:]+$/',
                message: 'Фильтры должны быть в формате "code:value:unit")'
            )
        ]
    )]
    protected array $filters = [];

    #[MongoDB\Field(type: "date")]
    protected DateTimeInterface $createdAt;

    #[MongoDB\Field(type: "date")]
    protected DateTimeInterface $updatedAt;

    #[MongoDB\Field(type: "bool")]
    protected bool $active = true;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public static function fromDto(ProductDto $productDto): static
    {
        $product = new static();
        $product->setCode($productDto->getCode());
        $product->setSectionCode($productDto->getSectionCode());
        $product->setName($productDto->getName());
        $product->setWeight($productDto->getWeight());
        $product->setVolume($productDto->getVolume());
        $product->setFilters($productDto->getFilters());

        return $product;
    }

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

    public function getSectionCode(): string
    {
        return $this->sectionCode;
    }

    public function setSectionCode(string $sectionCode): void
    {
        $this->sectionCode = $sectionCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getWeight(): string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): void
    {
        $this->weight = $weight;
    }

    public function getVolume(): string
    {
        return $this->volume;
    }

    public function setVolume(string $volume): void
    {
        $this->volume = $volume;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param ProductFilterInterface[] $filters
     * @return void
     */
    public function setFilters(array $filters): void
    {
        $this->filters = array_map(
            static fn(ProductFilterInterface $filter) => sprintf(
                "%s:%s:%s",
                $filter->getCode(),
                $filter->getValue(),
                $filter->getUnit()
            ),
            $filters
        );
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
