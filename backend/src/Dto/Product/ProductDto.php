<?php

namespace App\Dto\Product;

use Symfony\Component\Validator\Constraints as Assert;

readonly class ProductDto
{
    #[Assert\NotBlank(message: "Не задан символьный код товара")]
    protected string $code;

    #[Assert\NotBlank(message: "Не задан символьный код категории товара")]
    protected string $sectionCode;

    #[Assert\NotBlank(message: "Не задано название товара")]
    protected string $name;

    protected string $weight;

    protected string $volume;

    #[Assert\All(new Assert\Type(ProductFilterDto::class))]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    protected array $filters;

    /**
     * Product constructor.
     */
    public function __construct(array $data)
    {
        $this->code = $data['code'] ?? '';
        $this->sectionCode = $data['sectionCode'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->weight = $data['weight'] ?? '';
        $this->volume = $data['volume'] ?? '';
        $this->filters = array_map(
            static fn(array $filter): ProductFilterDto => new ProductFilterDto($filter),
            $data['filters']
        ) ?? [];
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSectionCode(): string
    {
        return $this->sectionCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWeight(): string
    {
        return $this->weight;
    }

    public function getVolume(): string
    {
        return $this->volume;
    }

    /**
     * @return ProductFilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
