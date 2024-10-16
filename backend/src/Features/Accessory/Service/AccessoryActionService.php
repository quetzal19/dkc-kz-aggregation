<?php

namespace App\Features\Accessory\Service;

use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Document\Accessory\Accessory;
use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\Accessory\DTO\Message\AccessoryMessageDTO;
use App\Features\Accessory\Repository\AccessoryRepository;
use App\Features\Message\Service\MessageValidatorService;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Features\TempStorage\Error\Type\ErrorType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use App\Helper\Abstract\Error\AbstractErrorMessage;

final readonly class AccessoryActionService implements ActionInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private SectionRepository $sectionRepository,
        private AccessoryRepository $accessoryRepository,
        private DocumentManager $documentManager,
        #[Autowire(service: 'monolog.logger.accessories')]
        private LoggerInterface $logger,
        private MessageValidatorService $messageValidatorService,
        #[Autowire(service: 'map.category.name.mapper')]
        private MapperMessageInterface $categoryNameMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var AccessoryMessageDTO $dto */
        $accessoryDocument = $this->accessoryRepository->findOneBy(['externalId' => $dto->id]);
        if ($accessoryDocument) {
            return new ErrorMessage(
                ErrorType::ENTITY_ALREADY_EXISTS,
                "On create accessory, accessory with external id '$dto->id' already exists"
            );
        }

        $element = $this->productRepository->findOneBy(['code' => $dto->elementCode]);
        if (!$element) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On create accessory, element with code '$dto->elementCode' not found"
            );
        }
        $accessory = $this->productRepository->findOneBy(['code' => $dto->accessoryCode]);
        $section = $this->sectionRepository->findOneBy(['code' => $dto->sectionCode]);
        if (!$section && !$accessory) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On create accessory, section with code '$dto->sectionCode' or accessory with code '$dto->accessoryCode' not found"
            );
        }

        $collectionCategory = new ArrayCollection();
        foreach ($dto->categoryName as $categoryName) {
            $categoryNameDocument = $this->categoryNameMapper->mapFromMessageDTO($categoryName);
            $collectionCategory->add($categoryNameDocument);
        }

        $accessoryDocument = new Accessory(
            externalId: $dto->id,
            element: $element,
            accessory: $accessory,
            section: $section,
            categoryName: $collectionCategory,
        );

        $this->documentManager->persist($accessoryDocument);

        $this->logger->info("Accessory created Id: $dto->id. message: " . json_encode($dto));

        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /**
         * @var AccessoryMessageDTO $dto
         * @var Accessory $accessoryDocument
         */
        $accessoryDocument = $this->accessoryRepository->findOneBy(['externalId' => $dto->id]);
        if (!$accessoryDocument) {
            $this->logger->warning(
                "On update accessory, accessory with external id '$dto->id' not found," .
                " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                return new ErrorMessage(
                    ErrorType::VALIDATION_ERROR,
                    'Post update accessory, validation for group create failed: ' . $ex->getMessage()
                );
            }

            return $this->create($dto);
        }

        $element = null;
        if (!empty($dto->elementCode)) {
            $element = $this->productRepository->findOneBy(['code' => $dto->elementCode]);
            if (!$element) {
                return new ErrorMessage(
                    ErrorType::DATA_NOT_READY,
                    "On update accessory, element with code '$dto->elementCode' not found"
                );
            }
        }

        $accessory = $this->productRepository->findOneBy(['code' => $dto->accessoryCode]);
        $section = $this->sectionRepository->findOneBy(['code' => $dto->sectionCode]);
        if (!$section && !$accessory) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On update accessory, section with code '$dto->sectionCode' or accessory with code '$dto->accessoryCode' not found"
            );
        }

        $collectionCategory = new ArrayCollection();
        foreach ($dto->categoryName as $categoryName) {
            $categoryNameDocument = $this->categoryNameMapper->mapFromMessageDTO($categoryName);
            $collectionCategory->add($categoryNameDocument);
        }

        if (!$collectionCategory->isEmpty()) {
            $accessoryDocument->setCategoryName($collectionCategory);
        }

        if ($element) {
            $accessoryDocument->setElement($element);
        }

        $accessoryDocument
            ->setAccessory($accessory)
            ->setSection($section);

        $this->logger->info("Accessory updated Id: $dto->id. message: " . json_encode($dto));

        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var AccessoryMessageDTO $dto */
        $this->accessoryRepository->delete($dto->id);

        $this->logger->info("Accessory with id '$dto->id' marked as deleted");

        return null;
    }
}