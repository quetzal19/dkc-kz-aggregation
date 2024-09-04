<?php

namespace App\Features\TempStorage\Message;

final readonly class TempStorageMessage
{
    public function __construct(
        public string $timestamp,
        public string $entity,
        public string $action,
        public array $message,
    ) {
    }
}