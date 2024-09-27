<?php

namespace App\Features\Analog\DTO\Message;

use App\Features\Category\DTO\Message\CategoryNameMessageDTO;
use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AnalogMessageDTO implements MessageDTOInterface
{
    /**
     * @param string $id
     * @param string|null $elementCode
     * @param string|null $analogCode
     * @param string|null $sectionCode
     * @param array|null $categoryName
     */
    public function __construct(
        #[Assert\NotBlank(message: 'id не может быть пустым', groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'id должен быть строкой', groups: ['create', 'update', 'delete'])]
        public mixed $id,

        #[Assert\Type(type: 'string', message: 'Код элемента должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $elementCode,

        #[Assert\Type(type: 'string', message: 'Код аналога должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $analogCode,

        #[Assert\Type(type: 'string', message: 'Код раздела должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $sectionCode,

        /** @var array<int, CategoryNameMessageDTO> $names */
        #[Assert\All(
            constraints: [
                new Assert\Type(type: CategoryNameMessageDTO::class, groups: ['create', 'update', 'delete']),
            ],
            groups: ['create', 'update', 'delete']
        )]
        #[Assert\Count(min: 1, groups: ['create'])]
        #[Assert\NotNull(message: 'Название и локаль не может быть null', groups: ['create', 'update'])]
        #[Assert\Valid(groups: ['create', 'update', 'delete'])]
        public mixed $categoryName,
    ) {
    }
}