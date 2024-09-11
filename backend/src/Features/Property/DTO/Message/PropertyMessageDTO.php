<?php

namespace App\Features\Property\DTO\Message;

use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PropertyMessageDTO implements MessageDTOInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'Код не может быть пустым', groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'Код должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $code,

        /** @var array<int, PropertyNameMessageDTO> $names */
        #[Assert\All(
            constraints: [
                new Assert\Type(type: PropertyNameMessageDTO::class, groups: ['create', 'update', 'delete']),
            ],
            groups: ['create', 'update', 'delete']
        )]
        #[Assert\Count(min: 1, groups: ['create'])]
        #[Assert\NotNull(message: 'Название и локаль не может быть null', groups: ['create', 'update'])]
        #[Assert\Valid(groups: ['create', 'update', 'delete'])]
        public mixed $names,
    ) {
    }
}