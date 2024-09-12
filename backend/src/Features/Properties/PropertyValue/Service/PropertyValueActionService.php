<?php

namespace App\Features\Properties\PropertyValue\Service;

use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use App\Features\Properties\PropertyValue\Mapper\PropertyValueMapper;
use App\Features\Properties\PropertyValue\Repository\PropertyValueRepository;
use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PropertyValueActionService implements ActionInterface
{
    /** @param PropertyValueMapper $propertyValueMapper */
    public function __construct(
        private LoggerInterface $logger,
        private PropertyValueRepository $repository,
        private DocumentManager $documentManager,
        #[Autowire(service: 'map.property.value.mapper')]
        private MapperMessageInterface $propertyValueMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): void
    {
        /** @var PropertyValueMessageDTO $dto */
        $propertyValue = $this->repository->findOneBy([
            'code' => $dto->code,
        ]);

        if ($propertyValue) {
            $this->logger->error(
                "On create propertyValue with code '$dto->code' already exists,"
                . " message: " . json_encode($dto)
            );
            return;
        }

        $propertyValue = $this->propertyValueMapper->mapFromMessageDTO($dto);

        $this->documentManager->persist($propertyValue);

        try {
            $this->documentManager->flush();
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }

        $this->logger->info("PropertyValue with code '$dto->code' created");
    }

    public function update(MessageDTOInterface $dto): void
    {
        /** @var PropertyValueMessageDTO $dto */
        $propertyValue = $this->repository->findOneBy([
            'code' => $dto->code,
        ]);

        if (!$propertyValue) {
            $this->logger->error(
                "On update propertyValue with code '$dto->code' not found,"
                . " message: " . json_encode($dto)
            );
            return;
        }

        $this->propertyValueMapper->mapFromMessageDTO($dto, $propertyValue);

        try {
            $this->documentManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        $this->logger->info("PropertyValue with code '$dto->code' updated");
    }

    public function delete(MessageDTOInterface $dto): void
    {
        /** @var PropertyValueMessageDTO $dto */
        $propertyValue = $this->repository->findOneBy([
            'code' => $dto->code,
        ]);

        if (!$propertyValue) {
            $this->logger->error(
                "On delete propertyValue with code '$dto->code' not found," .
                " message: " . json_encode($dto)
            );
            return;
        }

        $this->documentManager->remove($propertyValue);

        try {
            $this->documentManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        $this->logger->info("PropertyValue with code '$dto->code' deleted");
    }
}