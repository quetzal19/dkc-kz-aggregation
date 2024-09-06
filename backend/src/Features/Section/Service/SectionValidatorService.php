<?php

namespace App\Features\Section\Service;

use App\Features\Section\DTO\Message\SectionMessageDTO;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class SectionValidatorService
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /** @throws ValidationFailedException  */
    public function validateMessageDTO(SectionMessageDTO $sectionMessageDTO, array $groups): void
    {
        $errors = $this->validator->validate($sectionMessageDTO, groups:  $groups);
        if (count($errors) > 0) {
            throw new ValidationFailedException($sectionMessageDTO, $errors);
        }
    }
}