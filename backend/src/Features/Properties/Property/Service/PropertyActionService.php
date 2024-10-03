<?php

namespace App\Features\Properties\Property\Service;

use App\Features\Message\Service\MessageValidatorService;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Properties\Property\DTO\Message\PropertyMessageDTO;
use App\Features\Properties\Property\{Mapper\PropertyMapper};
use App\Helper\Interface\{ActionInterface,
    Mapper\MapperMessageInterface,
    Message\MessageDTOInterface
};
use Doctrine\ODM\MongoDB\{MongoDBException, DocumentManager};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class PropertyActionService implements ActionInterface
{

    /** @param PropertyMapper $propertyMapper */
    public function __construct(
        private LoggerInterface $logger,
        private DocumentManager $documentManager,
        private PropertyRepository $propertyRepository,
        #[Autowire(service: 'map.property.mapper')]
        private MapperMessageInterface $propertyMapper,
        private MessageValidatorService $messageValidatorService,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if ($property) {
            $this->logger->error(
                "On create property with code '$dto->code' property already exists," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $newProperty = $this->propertyMapper->mapFromMessageDTO($dto);

        $this->documentManager->persist($newProperty);

        $this->logger->info("Property with code '$dto->code' created");

        return true;
    }

    public function update(MessageDTOInterface $dto): bool
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if (!$property) {
            $this->logger->error(
                "On update property with code '$dto->code' property not found," .
                " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                $this->logger->error(
                    'Post update property, validation for group create failed: ' . $ex->getMessage(
                    ) . ", message: " . json_encode($dto)
                );
                return false;
            }

            return $this->create($dto);
        }

        $this->propertyMapper->mapFromMessageDTO($dto, $property);

        $this->logger->info("Property with code '$dto->code' updated");
        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if (!$property) {
            $this->logger->error(
                "On delete property with code '$dto->code' property not found," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $this->documentManager->remove($property);

        $this->logger->info("Property with code '$dto->code' deleted");
        return true;
    }
}