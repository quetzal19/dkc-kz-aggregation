<?php

namespace App\Features\TempStorage\Service;

use App\Features\TempStorage\DTO\TempStorageDTO;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class TempStorageValidatorService
{
    private static array $actionPriorities;
    private static array $entityPriorities;

    public function __construct(
        #[Autowire('%app.entity_priorities%')]
        array $entityPriorities,

        #[Autowire('%app.action_priorities%')]
        array $actionPriorities,

        private readonly ValidatorInterface $validator,
    ) {
        self::$entityPriorities = $entityPriorities;
        self::$actionPriorities = $actionPriorities;
    }

    public function validateDTO(TempStorageDTO $storageDTO): void
    {
        $errors = $this->validator->validate($storageDTO);
        if (count($errors) > 0) {
            throw new ValidationFailedException($storageDTO, $errors);
        }
    }

    public static function validateEntityType(mixed $entity, ExecutionContextInterface $context): void
    {
        self::validateType($entity, self::$entityPriorities, $context);
    }

    public static function validateActionType(mixed $action, ExecutionContextInterface $context): void
    {
        self::validateType($action, self::$actionPriorities, $context);
    }

    public static function validateType(mixed $type, array $types, ExecutionContextInterface $context): void
    {
        if (!is_string($type)) {
            $context
                ->buildViolation('Неверный тип данных')
                ->addViolation();
            return;
        }

        $typeNames = array_keys($types);
        if (!in_array($type, $typeNames)) {
            $context
                ->buildViolation("Неизвестный тип '$type' ожидается - " . join(', ', $typeNames))
                ->addViolation();
        }
    }
}