<?php

namespace App\Message;

class ProductImport
{
    protected string $message;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): ProductImport
    {
        $this->message = $message;

        return $this;
    }
}
