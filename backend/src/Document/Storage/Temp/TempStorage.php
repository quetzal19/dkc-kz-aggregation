<?php

namespace App\Document\Storage\Temp;

use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\TempStorage\Repository\TempStorageRepository;
use App\Helper\Interface\Storage\StorageInterface;
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\Index(keys: ['entity' => 'asc'])]
#[MongoDB\Document(repositoryClass: TempStorageRepository::class)]
class TempStorage implements StorageInterface
{
    #[MongoDB\Id(type: Type::STRING, strategy: 'UUID')]
    private string $id;

    #[MongoDB\Field(type: Type::DATE, nullable: true)]
    private ?\DateTime $errorDate = null;

    public function __construct(
        #[MongoDB\Field(type: Type::STRING)]
        private string $timestamp,

        #[MongoDB\Field(type: Type::STRING)]
        private string $entity,

        #[MongoDB\Field(type: Type::STRING)]
        private string $action,

        #[MongoDB\Field(type: Type::INT)]
        private int $actionPriority,

        #[MongoDB\Field(type: Type::INT)]
        private int $priority,

        #[MongoDB\Field(type: Type::STRING)]
        private string $message,

        #[MongoDB\EmbedOne(targetDocument: ErrorMessage::class)]
        private ?ErrorMessage $errorMessage = null,
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

    public function getActionPriority(): int
    {
        return $this->actionPriority;
    }

    public function setActionPriority(int $actionPriority): void
    {
        $this->actionPriority = $actionPriority;
    }

    public function getErrorMessage(): ?ErrorMessage
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?ErrorMessage $errorMessage): TempStorage
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getErrorDate(): ?\DateTime
    {
        return $this->errorDate;
    }

    public function setErrorDate(?\DateTime $errorDate): TempStorage
    {
        $this->errorDate = $errorDate;
        return $this;
    }

    public function __toString(): string
    {
        $arrayProperties = [
            'id' => $this->id,
            'timestamp' => $this->timestamp,
            'entity' => $this->entity,
            'action' => $this->action,
            'actionPriority' => $this->actionPriority,
            'priority' => $this->priority,
            'message' => $this->message
        ];
        return json_encode($arrayProperties, JSON_PRETTY_PRINT);
    }
}