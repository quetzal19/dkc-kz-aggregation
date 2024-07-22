<?php

namespace App\Document;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass=ProductRepository::class)
 */
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

    protected bool $active = true;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
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
