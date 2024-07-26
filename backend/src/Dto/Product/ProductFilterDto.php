<?php

namespace App\Dto\Product;

use Symfony\Component\Validator\Constraints as Assert;

readonly class ProductFilterDto implements ProductFilterInterface
{
    #[Assert\NotBlank(message: 'Не задан код фильтра')]
    public string $code;

    #[Assert\NotBlank(message: 'Не задано значение фильтра')]
    public string $value;

    #[Assert\NotBlank(message: 'Не задана единица измерения фильтра')]
    public string $unit;

    public function __construct(array $filterData)
    {
        $this->code = $filterData['code'] ?? '';
        $this->value = $filterData['value'] ?? '';
        $this->unit = $filterData['unit'] ?? '';
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }
}
