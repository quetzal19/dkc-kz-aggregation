<?php

namespace App\Features\Properties\Property\Service;

use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\Message\Service\MessageValidatorService;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Properties\Property\DTO\Message\PropertyMessageDTO;
use App\Features\TempStorage\Error\Type\ErrorType;
use App\Features\Properties\Property\{Mapper\PropertyMapper};
use App\Helper\Interface\{ActionInterface,
    Mapper\MapperMessageInterface,
    Message\MessageDTOInterface
};
use Doctrine\ODM\MongoDB\{MongoDBException, DocumentManager};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use App\Helper\Abstract\Error\AbstractErrorMessage;

final readonly class PropertyActionService implements ActionInterface
{

    /** @param PropertyMapper $propertyMapper */
    public function __construct(
        #[Autowire(service: 'monolog.logger.property_feature')]
        private LoggerInterface $logger,
        private DocumentManager $documentManager,
        private PropertyRepository $propertyRepository,
        #[Autowire(service: 'map.property.mapper')]
        private MapperMessageInterface $propertyMapper,
        private MessageValidatorService $messageValidatorService,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if ($property) {
            return new ErrorMessage(
                ErrorType::ENTITY_ALREADY_EXISTS,
                "On create property with code '$dto->code' property already exists"
            );
        }

        $newProperty = $this->propertyMapper->mapFromMessageDTO($dto);

        $this->documentManager->persist($newProperty);

        $this->logger->info("Property with code '$dto->code' created");

        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if (!$property) {
            $this->logger->warning(
                "On update property with code '$dto->code' property not found," .
                " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                return new ErrorMessage(
                    ErrorType::VALIDATION_ERROR,
                    'Post update property, validation for group create failed: ' . $ex->getMessage()
                );
            }

            return $this->create($dto);
        }

        $this->propertyMapper->mapFromMessageDTO($dto, $property);

        $this->logger->info("Property with code '$dto->code' updated");
        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if (!$property) {
            return new ErrorMessage(
                ErrorType::ENTITY_NOT_FOUND,
                "On delete property with code '$dto->code' property not found"
            );
        }

        $this->documentManager->remove($property);

        $this->logger->info("Property with code '$dto->code' deleted");
        return null;
    }
}