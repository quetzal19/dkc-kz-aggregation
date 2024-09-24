<?php

namespace App\Features\Properties\Property\DTO\Http\Response;

use App\Helper\Enum\FilterListType;

final class PropertyFilterItemResponseDTO
{
    public function __construct(
        private string $code,
        private ?string $name = null,
        private string $type = FilterListType::LIST->value,
        private int $count = 0,
        /** @var PropertyFilterItemValueResponseDTO[] $values */
        private array $values = [],
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): PropertyFilterItemResponseDTO
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): PropertyFilterItemResponseDTO
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): PropertyFilterItemResponseDTO
    {
        $this->type = $type;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): PropertyFilterItemResponseDTO
    {
        $this->count = $count;
        return $this;
    }

    public function addValue(PropertyFilterItemValueResponseDTO $value): PropertyFilterItemResponseDTO
    {
        $this->values[] = $value;
        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): PropertyFilterItemResponseDTO
    {
        $this->values = $values;
        return $this;
    }
}