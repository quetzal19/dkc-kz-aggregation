<?php

namespace App\Helper\Interface;

use App\Helper\Interface\Storage\StorageInterface;

interface EntityHandlerStorageInterface
{
    public function handle(string $message, string $action): bool;
}