<?php

namespace App\Features\Properties\PropertyFeatureMap\DTO\Message;

use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PropertyFeatureMapMessageDTO implements MessageDTOInterface
{
    public function __construct(
        #[Assert\NotBlank(groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'Код ед. измерения должно быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        #[Assert\Length(
            min: 1, max: 255,
            minMessage: 'Код ед. измерения не может быть пустым',
            maxMessage: 'Код ед. измерения не должен превышать 255 символов',
            groups: [
                'create',
                'update',
                'delete'
            ]
        )]
        public mixed $unitCode,

        /** @var PropertyFeatureMapPrimaryKeyDTO $primaryKeys */
        #[Assert\NotBlank(groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: PropertyFeatureMapPrimaryKeyDTO::class, groups: ['create', 'update', 'delete'])]
        #[Assert\Valid(groups: ['create', 'update', 'delete'])]
        public mixed $primaryKeys,
    ) {
    }
}