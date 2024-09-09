<?php

namespace App\Features\Message\Service;

use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class MessageValidatorService
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /** @throws ValidationFailedException  */
    public function validateMessageDTO(MessageDTOInterface $messageDTO, array $groups): void
    {
        $errors = $this->validator->validate($messageDTO, groups:  $groups);
        if (count($errors) > 0) {
            throw new ValidationFailedException($messageDTO, $errors);
        }
    }
}