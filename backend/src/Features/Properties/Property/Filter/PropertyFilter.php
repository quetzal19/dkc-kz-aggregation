<?php

namespace App\Features\Properties\Property\Filter;

use App\Features\Properties\Property\Service\PropertyValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PropertyFilter
{
    public function __construct(
        #[Assert\Type(type: 'string')]
        #[Assert\NotNull(message: 'Не может быть пустым')]
        #[Assert\NotBlank(message: 'Не может быть пустым')]
        public ?string $sectionCode = null,

        #[Assert\Callback(callback: [PropertyValidatorService::class, 'validateFilters'])]
        public ?string $filters = null,
    )
    {
    }
}