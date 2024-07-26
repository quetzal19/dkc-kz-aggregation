<?php

namespace App\Message;

/**
 * Тестовое сообщение для импорта продуктов, удалить при необходимости
 */
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
