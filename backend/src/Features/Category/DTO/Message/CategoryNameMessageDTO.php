<?php

namespace App\Features\Category\DTO\Message;

use App\Helper\Enum\LocaleType;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CategoryNameMessageDTO
{
    public function __construct(
        #[Assert\Type(type: 'string', message: 'Название должно быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $name,

        #[Assert\NotBlank(message: 'Локализация не может быть пустым', groups: ['create', 'update'])]
        #[Assert\Type(
            type: 'string',
            message: 'Локализация должна быть строкой',
            groups: ['create', 'update', 'delete']
        )]
        #[Assert\Choice(
            callback: [LocaleType::class, 'getNamesLocale'],
            message: 'Некорректная локализация',
            groups: ['create', 'update', 'delete']
        )]
        public mixed $locale,
    ) {
    }
}