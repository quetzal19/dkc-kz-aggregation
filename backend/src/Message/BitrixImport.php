<?php

namespace App\Message;

use App\Enum\Action;
use App\Enum\Entity;

class BitrixImport
{
    protected string $timestamp;

    protected Entity $entity;

    protected Action $action;

    protected string $message;

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    public function getAction(): Action
    {
        return $this->action;
    }

    public function setAction(Action $action): void
    {
        $this->action = $action;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
