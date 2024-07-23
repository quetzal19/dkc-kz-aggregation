<?php

namespace App\Dto;

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

    #[Assert\All(
        new Assert\Collection(
            fields: [
                'code' => [
                    new Assert\Type(
                        type: 'string',
                        message: 'Код фильтра должен быть строкой'
                    ),
                    new Assert\NotBlank(
                        message: 'Не задан код фильтра'
                    ),
                ],
                'value' => [
                    new Assert\Type(
                        type: 'string',
                        message: 'Значение фильтра должно быть строкой'
                    ),
                    new Assert\NotBlank(
                        message: 'Не задано значение фильтра'
                    ),
                ],
                'unit' => [
                    new Assert\Type(
                        type: 'string',
                        message: 'Единица измерения фильтра должна быть строкой'
                    ),
                    new Assert\NotBlank(
                        message: 'Не задана единица измерения фильтра'
                    ),
                ]
            ],
            allowMissingFields: false
        )
    )]
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
        $this->filters = $data['filters'] ?? [];
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

    public function getFilters(): array
    {
        return $this->filters;
    }
}
