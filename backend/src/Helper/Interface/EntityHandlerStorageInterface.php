<?php

namespace App\Helper\Interface;

use App\Helper\Abstract\Error\AbstractErrorMessage;

interface EntityHandlerStorageInterface
{
    public function handle(string $message, string $action): ?AbstractErrorMessage;
}