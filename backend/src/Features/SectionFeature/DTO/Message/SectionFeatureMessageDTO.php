<?php

namespace App\Features\SectionFeature\DTO\Message;

use App\Helper\Common\IntegerHelper;
use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SectionFeatureMessageDTO implements MessageDTOInterface
{
    public function __construct(
        /** @var SectionFeaturePrimaryKeyDTO $primaryKeys */
        #[Assert\NotBlank(groups: ['create', 'update', 'delete'])]
        #[Assert\Type(type: SectionFeaturePrimaryKeyDTO::class, groups: ['create', 'update', 'delete'])]
        #[Assert\Valid(groups: ['create', 'update', 'delete'])]
        public mixed $primaryKeys,

        #[Assert\Type(type: 'integer', message: 'Сортировка должна быть строкой', groups: ['create', 'update', 'delete'])]
        #[Assert\PositiveOrZero(message: 'Сортировка должна быть положительной', groups: ['create', 'update', 'delete'])]
        #[Assert\LessThan(value: IntegerHelper::MAX_SIZE_INTEGER, message: 'Сортировка должна быть меньше 2147483647', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $sort,
    ) {
    }
}