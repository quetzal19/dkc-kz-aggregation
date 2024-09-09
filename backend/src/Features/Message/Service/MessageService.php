<?php

namespace App\Features\Message\Service;

use App\Helper\Interface\Message\MessageDTOInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class MessageService
{
    public function __construct(
        private SerializerInterface $serializer,
        private MessageValidatorService $validatorService,
        private LoggerInterface $logger
    )
    {
    }

    public function serializeToDTOAndValidate(string $message, array $groups, string $typeDTO, string $entity): ?MessageDTOInterface
    {
        /** @var MessageDTOInterface $dto */
        $dto = $this->serializer->deserialize(
            $message, $typeDTO, 'json'
        );

        try {
            $this->validatorService->validateMessageDTO($dto, $groups);
        } catch (ValidationFailedException $e) {
            $this->logger->error(
                'Validation "' . $entity .  '"  failed for group "' . implode(',', $groups) . '": ' .
                $e->getMessage() . ", message: $message"
            );
            return null;
        }



        return $dto;
    }
}