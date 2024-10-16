<?php

namespace App\Features\ProductFeature\DTO\Message;

use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductFeaturePrimaryKeyDTO implements MessageDTOInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'Код продукта не может быть пустым', groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'Код продукта должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        #[Assert\Length(
            min: 1, max: 255,
            minMessage: 'Код продукта должен содержать не менее 1 символа',
            maxMessage: 'Код продукта должен содержать не более 255 символов',
            groups: ['create', 'update', 'delete']
        )]
        public mixed $productCode,

        #[Assert\NotBlank(message: 'Код свойства не может быть пустым', groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'Код свойства должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        #[Assert\Length(
            min: 1, max: 255,
            minMessage: 'Код свойства должен содержать не менее 1 символа',
            maxMessage: 'Код свойства должен содержать не более 255 символов',
            groups: ['create', 'update', 'delete']
        )]
        public mixed $featureCode,
    ) {
    }
}