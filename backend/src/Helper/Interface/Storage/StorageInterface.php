<?php

namespace App\Helper\Interface\Storage;

interface StorageInterface
{
    public function getAction(): string;
    public function getMessage(): string;

}