<?php

namespace App\Features\Accessory\DTO\Message;

use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AccessoryMessageDTO implements MessageDTOInterface
{
    /**
     * @param string $id
     * @param string|null $elementCode
     * @param string|null $accessoryCode
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

        #[Assert\Type(type: 'string', message: 'Код аксессуара должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $accessoryCode,

        #[Assert\Type(type: 'string', message: 'Код раздела должен быть строкой', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $sectionCode,

        #[Assert\Type(type: 'array', message: 'Название категории должно быть массивом', groups: [
            'create',
            'update',
            'delete'
        ])]
        public mixed $categoryName
    ) {
    }
}