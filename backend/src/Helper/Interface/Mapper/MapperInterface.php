<?php

namespace App\Helper\Interface\Mapper;

interface MapperInterface
{
    public function mapFromDTO(mixed $dto): object;
}