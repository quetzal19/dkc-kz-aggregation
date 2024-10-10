<?php

namespace App\Helper\Interface;

use App\Helper\Abstract\Error\AbstractErrorMessage;
use App\Helper\Interface\Message\MessageDTOInterface;

interface ActionInterface
{
    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage;
    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage;
    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage;
}