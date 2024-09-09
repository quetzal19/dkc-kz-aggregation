<?php

namespace App\Helper\Interface;

use App\Helper\Interface\Message\MessageDTOInterface;

interface ActionInterface
{
    public function create(MessageDTOInterface $dto): void;
    public function update(MessageDTOInterface $dto): void;
    public function delete(MessageDTOInterface $dto): void;
}