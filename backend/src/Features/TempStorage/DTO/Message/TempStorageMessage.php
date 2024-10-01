<?php

namespace App\Features\TempStorage\DTO\Message;

final readonly class TempStorageMessage
{
    public function __construct(
        public array|object|null $data,
    ) {
    }
}