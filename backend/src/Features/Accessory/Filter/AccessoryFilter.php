<?php

namespace App\Features\Accessory\Filter;

use App\Helper\Validator\ValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AccessoryFilter
{
    public function __construct(
        #[Assert\Type(type: 'string', message: 'Значение должно быть строкой')]
        #[Assert\NotNull(message: 'Не может быть пустым')]
        #[Assert\NotBlank(message: 'Не может быть пустым')]
        public ?string $productCode = null,

        /** @var int $page */
        #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'])]
        #[Assert\Callback(callback: [ValidatorService::class, 'validatePage'])]
        public mixed $page = 1,

        /** @var int $limit */
        #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'])]
        #[Assert\Callback(callback: [ValidatorService::class, 'validateLimit'])]
        public mixed $limit = 4,

        #[Assert\Type(type: 'string', message: 'Значение должно быть строкой')]
        #[Assert\Length(max: 255, maxMessage: 'Максимальная длина 255 символов')]
        public ?string $sectionName = null,
    )
    {
    }
}