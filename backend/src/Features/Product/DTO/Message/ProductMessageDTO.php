<?php

namespace App\Features\Product\DTO\Message;

use App\Helper\Common\IntegerHelper;
use App\Helper\Enum\LocaleType;
use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductMessageDTO implements MessageDTOInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'Локализация не может быть пустым', groups: ['create', 'update', 'delete'])]
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

        #[Assert\NotBlank(message: 'Идентификатор продукта не может быть пустым', groups: [
            'create',
            'update',
        ])]
        #[Assert\Type(
            type: 'string',
            message: 'Идентификатор продукта должен быть строкой',
            groups: ['create', 'update', 'delete']
        )]
        #[Assert\Length(
            max: 50,
            maxMessage: 'Идентификатор продукта не может быть больше 50 символов',
            groups: ['create', 'update', 'delete']
        )]
        public mixed $id,

        #[Assert\NotBlank(message: 'Код секции не может быть пустым', groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'Код секции должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $code,

        #[Assert\NotBlank(message: 'Идентификатор раздела не может быть пустым', groups: ['create'])]
        #[Assert\Type(
            type: 'string',
            message: 'Идентификатор раздела должен быть строкой',
            groups: ['create', 'update', 'delete']
        )]
        #[Assert\Length(
            max: 50,
            maxMessage: 'Идентификатор раздела не может быть больше 50 символов',
            groups: ['create', 'update', 'delete']
        )]
        public mixed $sectionId,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Type(
            type: 'bool',
            message: 'Активность должна быть булевым значением',
            groups: ['create', 'update']
        )]
        public mixed $active,

        #[Assert\NotBlank(message: 'Сортировка не может быть пустым', groups: ['create'])]
        #[Assert\Type(type: 'integer', message: 'Сортировка должна быть целым числом', groups: ['create', 'update'])]
        #[Assert\PositiveOrZero(message: 'Сортировка должна быть положительной', groups: ['create', 'update'])]
        #[Assert\LessThan(value: IntegerHelper::MAX_SIZE_INTEGER, message: 'Сортировка должна быть меньше 2147483647', groups: [
            'create',
            'update'
        ])]
        public mixed $sort,

        #[Assert\Type(type: 'string', message: 'Вес продукта должен быть строкой', groups: ['create', 'update'])]
        #[Assert\Length(
            max: 30,
            maxMessage: 'Вес продукта не может быть больше 30 символов',
            groups: ['create', 'update']
        )]
        public mixed $weight,

        #[Assert\Type(type: 'string', message: 'Объем продукта должен быть строкой', groups: ['create', 'update'])]
        #[Assert\Length(
            max: 30,
            maxMessage: 'Объем продукта не может быть больше 30 символов',
            groups: ['create', 'update']
        )]
        public mixed $volume,

        #[Assert\NotBlank(message: 'Идентификатор EtimArtClass не может быть пустым', groups: ['create'])]
        #[Assert\Type(type: 'string', message: 'Идентификатор EtimArtClass должен быть строкой', groups: [
            'create',
            'update'
        ])]
        #[Assert\Length(
            max: 50,
            maxMessage: 'Идентификатор EtimArtClass не может быть больше 50 символов',
            groups: ['create', 'update']
        )]
        public mixed $etimArtClassId,
    ) {
    }
}