<?php

namespace App\Message;

class ProductImport
{
    /**
     * @var string
     */
    private string $message;

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ProductImport
     */
    public function setMessage(string $message): ProductImport
    {
        $this->message = $message;
        return $this;
    }
}
