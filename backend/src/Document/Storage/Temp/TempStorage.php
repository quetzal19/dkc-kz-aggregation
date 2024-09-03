<?php

namespace App\Document\Storage\Temp;

use App\Features\TempStorage\Repository\TempStorageRepository;
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\Document(repositoryClass: TempStorageRepository::class)]
class TempStorage
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private string $id;

    public function __construct(
        #[MongoDB\Field(type: Type::STRING)]
        private string $timestamp,

        #[MongoDB\Field(type: Type::STRING)]
        private string $entity,

        #[MongoDB\Field(type: Type::STRING)]
        private string $action,

        #[MongoDB\Field(type: Type::INT)]
        private int $priority,

        #[MongoDB\Field(type: Type::STRING)]
        private string $message,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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