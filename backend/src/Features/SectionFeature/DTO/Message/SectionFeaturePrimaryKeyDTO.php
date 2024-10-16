<?php

namespace App\Features\SectionFeature\DTO\Message;

use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SectionFeaturePrimaryKeyDTO implements MessageDTOInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'Код раздела не может быть пустым', groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: 'string', message: 'Код раздела должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        #[Assert\Length(
            min: 1, max: 255,
            minMessage: 'Код раздела должен содержать не менее 1 символа',
            maxMessage: 'Код раздела должен содержать не более 255 символов',
            groups: ['create', 'update', 'delete']
        )]
        public mixed $sectionCode,

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