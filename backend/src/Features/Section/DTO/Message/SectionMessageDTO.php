<?php

namespace App\Features\Section\DTO\Message;

use App\Helper\Enum\LocaleType;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SectionMessageDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Локализация не может быть пустым', groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'Локализация должна быть строкой', groups: ['create', 'update', 'delete'])]
        #[Assert\Choice(
            callback: [LocaleType::class, 'getNamesLocale'],
            message: 'Некорректная локализация',
            groups: ['create', 'update', 'delete']
        )]
        public mixed $locale,

        #[Assert\Type(
            type: 'string',
            message: 'Идентификатор родительской секции должен быть строкой',
            groups: ['create', 'update']
        )]
        #[Assert\Length(
            max: 50,
            maxMessage: 'Идентификатор родительской секции не может быть больше 50 символов',
            groups: ['create', 'update']
        )]
        public mixed $parentId,

        #[Assert\NotBlank(groups: ['create'])]
        #[Assert\Type(
            type: 'bool',
            message: 'Активность должна быть булевым значением',
            groups: ['create', 'update']
        )]
        public mixed $active,

        #[Assert\NotBlank(message: 'Сортировка не может быть пустым', groups: ['create'])]
        #[Assert\Type(type: 'integer', message: 'Сортировка должна быть строкой', groups: ['create', 'update'])]
        #[Assert\PositiveOrZero(message: 'Сортировка должна быть положительной', groups: ['create', 'update'])]
        #[Assert\LessThan(value: 1000, message: 'Сортировка должна быть меньше 1000', groups: ['create', 'update'])]
        public mixed $sort,


        #[Assert\NotBlank(message: 'Название секции не может быть пустым', groups: ['create'])]
        #[Assert\Type(type: 'string', message: 'Название секции должно быть строкой', groups: ['create', 'update'])]
        #[Assert\Length(
            max: 300,
            maxMessage: 'Название секции не может быть больше 300 символов',
            groups: ['create', 'update']
        )]
        public mixed $name,


        #[Assert\NotBlank(message: 'Код секции не может быть пустым', groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'Код секции должен быть строкой', groups: ['create', 'update', 'delete'])]
        public mixed $code,
    ) {
    }
}