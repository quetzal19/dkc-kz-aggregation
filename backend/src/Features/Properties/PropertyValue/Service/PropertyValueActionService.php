<?php

namespace App\Features\Properties\PropertyValue\Service;

use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\Message\Service\MessageValidatorService;
use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use App\Features\Properties\PropertyValue\Mapper\PropertyValueMapper;
use App\Features\Properties\PropertyValue\Repository\PropertyValueRepository;
use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Features\TempStorage\Error\Type\ErrorType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use App\Helper\Abstract\Error\AbstractErrorMessage;

final readonly class PropertyValueActionService implements ActionInterface
{
    /** @param PropertyValueMapper $propertyValueMapper */
    public function __construct(
        #[Autowire(service: 'monolog.logger.property_value')]
        private LoggerInterface $logger,
        private PropertyValueRepository $repository,
        private DocumentManager $documentManager,
        #[Autowire(service: 'map.property.value.mapper')]
        private MapperMessageInterface $propertyValueMapper,
        private MessageValidatorService $messageValidatorService,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyValueMessageDTO $dto */
        $propertyValue = $this->repository->findOneBy([
            'code' => $dto->code,
        ]);

        if ($propertyValue) {
            return new ErrorMessage(
                ErrorType::ENTITY_ALREADY_EXISTS,
                "On create propertyValue with code '$dto->code' already exists"
            );
        }

        $propertyValue = $this->propertyValueMapper->mapFromMessageDTO($dto);

        $this->documentManager->persist($propertyValue);
        $this->logger->info("PropertyValue with code '$dto->code' created");
        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyValueMessageDTO $dto */
        $propertyValue = $this->repository->findOneBy([
            'code' => $dto->code,
        ]);

        if (!$propertyValue) {
            $this->logger->warning(
                "On update propertyValue with code '$dto->code' not found,"
                . " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                return new ErrorMessage(
                    ErrorType::VALIDATION_ERROR,
                    'Post update propertyValue, validation for group create failed: ' . $ex->getMessage()
                );
            }

            return $this->create($dto);
        }

        $this->propertyValueMapper->mapFromMessageDTO($dto, $propertyValue);

        $this->logger->info("PropertyValue with code '$dto->code' updated");
        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyValueMessageDTO $dto */
        $propertyValue = $this->repository->findOneBy([
            'code' => $dto->code,
        ]);

        if (!$propertyValue) {
            return new ErrorMessage(
                ErrorType::ENTITY_NOT_FOUND,
                "On delete propertyValue with code '$dto->code' not found"
            );
        }

        $this->documentManager->remove($propertyValue);

        $this->logger->info("PropertyValue with code '$dto->code' deleted");
        return null;
    }
}