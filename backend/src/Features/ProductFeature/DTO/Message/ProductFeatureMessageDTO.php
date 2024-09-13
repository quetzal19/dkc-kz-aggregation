<?php

namespace App\Features\ProductFeature\DTO\Message;

use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductFeatureMessageDTO implements MessageDTOInterface
{
    public function __construct(
        /** @var ProductFeaturePrimaryKeyDTO $primaryKeys */
        #[Assert\NotBlank(groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: ProductFeaturePrimaryKeyDTO::class, groups: ['create', 'update', 'delete'])]
        #[Assert\Valid(groups: ['create', 'update', 'delete'])]
        public mixed $primaryKeys,

        #[Assert\Type(type: 'string', message: 'Значение должно быть строкой', groups: ['create', 'update', 'delete'])]
        #[Assert\Length(max: 255, maxMessage: 'Значение не должно превышать 255 символов', groups: ['create', 'update', 'delete'])]
        public mixed $value,

        #[Assert\Type(type: 'string', message: 'Код значения должно быть строкой', groups: ['create', 'update', 'delete'])]
        #[Assert\Length(max: 255, maxMessage: 'Код значения не должен превышать 255 символов', groups: ['create', 'update', 'delete'])]
        public mixed $valueCode,
    )
    {
    }
}