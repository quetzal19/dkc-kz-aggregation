<?php

namespace App\Features\Message\Service;

use App\Helper\Interface\Message\MessageDTOInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class MessageService
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private MessageValidatorService $validatorService,
    ) {
    }

    public function serializeToDTOAndValidate(
        array $message,
        array $groups,
        string $typeDTO
    ): ?MessageDTOInterface {
        /** @var MessageDTOInterface $dto */
        $dto = $this->denormalizer->denormalize(
            $message,
            $typeDTO,
        );

        $this->validatorService->validateMessageDTO($dto, $groups);
        return $dto;
    }
}