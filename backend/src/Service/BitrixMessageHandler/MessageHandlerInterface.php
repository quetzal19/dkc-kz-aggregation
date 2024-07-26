<?php

namespace App\Service\BitrixMessageHandler;

use App\Message\BitrixImport;

interface MessageHandlerInterface
{
    public function handleMessage(BitrixImport $message): void;
}