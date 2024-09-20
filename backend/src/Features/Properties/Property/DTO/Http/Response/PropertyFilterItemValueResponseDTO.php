<?php

namespace App\Features\Properties\Property\DTO\Http\Response;

final class PropertyFilterItemValueResponseDTO
{
    public function __construct(
        private string $code,
        private string $name,
        private int $count = 0,
        private string $enabled = 'true',
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): PropertyFilterItemValueResponseDTO
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): PropertyFilterItemValueResponseDTO
    {
        $this->name = $name;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function incrementCount(): PropertyFilterItemValueResponseDTO
    {
        $this->count++;
        return $this;
    }

    public function setCount(int $count): PropertyFilterItemValueResponseDTO
    {
        $this->count = $count;
        return $this;
    }

    public function getEnabled(): string
    {
        return $this->enabled;
    }

    public function setEnabled(string $enabled): PropertyFilterItemValueResponseDTO
    {
        $this->enabled = $enabled;
        return $this;
    }

}