<?php

namespace App\Features\Property\Service;

use App\Features\Property\DTO\Message\PropertyMessageDTO;
use App\Features\Property\{Mapper\PropertyMapper, Repository\PropertyRepository};
use App\Helper\Interface\{ActionInterface,
    Mapper\MapperMessageInterface,
    Message\MessageDTOInterface
};
use Doctrine\ODM\MongoDB\{MongoDBException, DocumentManager};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PropertyActionService implements ActionInterface
{

    /** @param PropertyMapper $propertyMapper */
    public function __construct(
        private LoggerInterface $logger,
        private DocumentManager $documentManager,
        private PropertyRepository $propertyRepository,
        #[Autowire(service: 'map.property.mapper')]
        private MapperMessageInterface $propertyMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): void
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if ($property) {
            $this->logger->error(
                "On create property with code '$dto->code' property already exists," .
                " message: " . json_encode($dto)
            );
            return;
        }

        $newProperty = $this->propertyMapper->mapFromMessageDTO($dto);

        $this->documentManager->persist($newProperty);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->logger->info("Property with code '$dto->code' created");
    }

    public function update(MessageDTOInterface $dto): void
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if (!$property) {
            $this->logger->error(
                "On update property with code '$dto->code' property not found," .
                " message: " . json_encode($dto)
            );
            return;
        }

        $this->propertyMapper->mapFromMessageDTO($dto, $property);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->logger->info("Property with code '$dto->code' updated");
    }

    public function delete(MessageDTOInterface $dto): void
    {
        /** @var PropertyMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy(['code' => $dto->code]);

        if (!$property) {
            $this->logger->error(
                "On delete property with code '$dto->code' property not found," .
                " message: " . json_encode($dto)
            );
            return;
        }

        $this->documentManager->remove($property);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->logger->info("Property with code '$dto->code' deleted");
    }
}