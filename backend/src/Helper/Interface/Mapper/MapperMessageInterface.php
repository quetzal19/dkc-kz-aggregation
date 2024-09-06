<?php

namespace App\Helper\Interface\Mapper;

interface MapperMessageInterface
{
    public function mapFromMessageDTO(mixed $dto, mixed $entity = null): object;
}