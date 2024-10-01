<?php

namespace App\Features\Product\Filter;

use App\Features\Properties\Property\Service\PropertyValidatorService;
use App\Helper\Validator\ValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductFilter
{
    public function __construct(
        #[Assert\Type(type: 'string')]
        #[Assert\NotNull(message: 'Не может быть пустым')]
        #[Assert\NotBlank(message: 'Не может быть пустым')]
        public ?string $sectionCode = null,

        /** @var int $page */
        #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'])]
        #[Assert\Callback(callback: [ValidatorService::class, 'validatePage'])]
        public mixed $page = 1,

        /** @var int $limit */
        #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'])]
        #[Assert\Callback(callback: [ValidatorService::class, 'validateLimit'])]
        public mixed $limit = 20,

        #[Assert\Callback(callback: [PropertyValidatorService::class, 'validateFilters'])]
        public ?string $filters = null,
    )
    {
    }
}