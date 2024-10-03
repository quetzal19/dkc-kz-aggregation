<?php

namespace App\Features\Properties\PropertyValue\Service;

use App\Features\Message\Service\MessageValidatorService;
use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use App\Features\Properties\PropertyValue\Mapper\PropertyValueMapper;
use App\Features\Properties\PropertyValue\Repository\PropertyValueRepository;
use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class PropertyValueActionService implements ActionInterface
{
    /** @param PropertyValueMapper $propertyValueMapper */
    public function __construct(
        private LoggerInterface $logger,
        private PropertyValueRepository $repository,
        private DocumentManager $documentManager,
        #[Autowire(service: 'map.property.value.mapper')]
        private MapperMessageInterface $propertyValueMapper,
        private MessageValidatorService $messageValidatorService,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
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
            return false;
        }

        $propertyValue = $this->propertyValueMapper->mapFromMessageDTO($dto);

        $this->documentManager->persist($propertyValue);
        $this->logger->info("PropertyValue with code '$dto->code' created");
        return true;
    }

    public function update(MessageDTOInterface $dto): bool
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

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                $this->logger->error(
                    'Post update propertyValue, validation for group create failed: ' . $ex->getMessage(
                    ) . ", message: " . json_encode($dto)
                );
                return false;
            }

            return $this->create($dto);
        }

        $this->propertyValueMapper->mapFromMessageDTO($dto, $propertyValue);

        $this->logger->info("PropertyValue with code '$dto->code' updated");
        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
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
            return false;
        }

        $this->documentManager->remove($propertyValue);

        $this->logger->info("PropertyValue with code '$dto->code' deleted");
        return true;
    }
}