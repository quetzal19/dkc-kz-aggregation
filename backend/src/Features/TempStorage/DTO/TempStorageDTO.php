<?php

namespace App\Features\TempStorage\DTO;

use App\Features\TempStorage\Service\TempStorageValidatorService;
use App\Helper\Validator\Attributes\IsValidJson;
use Symfony\Component\Validator\Constraints as Assert;

readonly class TempStorageDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "Не должен быть пустым")]
        #[Assert\Type(type: 'string', message: "Неверный тип данных")]
        #[Assert\Length(
            min: 10, max: 11,
            minMessage: 'Минимальная длина 10 символов',
            maxMessage: 'Максимальная длина 11 символов'
        )]
        public mixed $timestamp,

        #[Assert\NotBlank(message: "Не должен быть пустым")]
        #[Assert\Callback(callback: [TempStorageValidatorService::class, 'validateEntityType'])]
        #[Assert\Type(type: 'string', message: "Неверный тип данных")]
        public mixed $entity,

        #[Assert\NotBlank(message: "Не должен быть пустым")]
        #[Assert\Callback(callback: [TempStorageValidatorService::class, 'validateActionType'])]
        #[Assert\Type(type: 'string', message: "Неверный тип данных")]
        public mixed $action,

        #[IsValidJson]
        #[Assert\NotBlank(message: "Не должен быть пустым")]
        public mixed $message,
    ) {
    }
}