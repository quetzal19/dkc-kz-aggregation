<?php

namespace App\Helper\Interface;

use App\Document\Storage\Temp\TempStorage;

interface EntityHandlerStorageInterface
{
    public function handle(TempStorage $storage): void;
}