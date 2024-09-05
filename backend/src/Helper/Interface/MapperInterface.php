<?php

namespace App\Helper\Interface;

interface MapperInterface
{
    public function mapFromDTO(mixed $dto): object;
}