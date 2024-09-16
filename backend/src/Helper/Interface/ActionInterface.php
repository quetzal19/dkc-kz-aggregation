<?php

namespace App\Helper\Interface;

use App\Helper\Interface\Message\MessageDTOInterface;

interface ActionInterface
{
    public function create(MessageDTOInterface $dto): bool;
    public function update(MessageDTOInterface $dto): bool;
    public function delete(MessageDTOInterface $dto): bool;
}