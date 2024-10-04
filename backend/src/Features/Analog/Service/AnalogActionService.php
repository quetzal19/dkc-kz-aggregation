<?php

namespace App\Features\Analog\Service;

use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Document\Analog\Analog;
use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\Analog\DTO\Message\AnalogMessageDTO;
use App\Features\Analog\Repository\AnalogRepository;
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

final readonly class AnalogActionService implements ActionInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private SectionRepository $sectionRepository,
        private AnalogRepository $analogRepository,
        private DocumentManager $documentManager,
        #[Autowire(service: 'monolog.logger.analogs')]
        private LoggerInterface $logger,
        #[Autowire(service: 'map.category.name.mapper')]
        private MapperMessageInterface $categoryNameMapper,
        private MessageValidatorService $messageValidatorService,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var AnalogMessageDTO $dto */
        $analogDocument = $this->analogRepository->findOneBy(['externalId' => $dto->id]);
        if ($analogDocument) {
            return new ErrorMessage(
                ErrorType::ENTITY_ALREADY_EXISTS,
                "On create analog, analog with id '$dto->id' already exists"
            );
        }

        $element = $this->productRepository->findOneBy(['code' => $dto->elementCode]);
        if (!$element) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On create analog, element with code '$dto->elementCode' not found"
            );
        }
        $analog = $this->productRepository->findOneBy(['code' => $dto->analogCode]);
        $section = $this->sectionRepository->findOneBy(['code' => $dto->sectionCode]);
        if (!$section && !$analog) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On create analog, section with code '$dto->sectionCode' or analog with code '$dto->analogCode' not found"
            );
        }

        $collectionCategory = new ArrayCollection();
        foreach ($dto->categoryName as $categoryName) {
            $categoryNameDocument = $this->categoryNameMapper->mapFromMessageDTO($categoryName);
            $collectionCategory->add($categoryNameDocument);
        }

        $analogDocument = new Analog(
            externalId: $dto->id,
            element: $element,
            analog: $analog,
            section: $section,
            categoryName: $collectionCategory,
        );

        $this->documentManager->persist($analogDocument);

        $this->logger->info("Analog with id '$dto->id' created, message: " . json_encode($dto));

        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var AnalogMessageDTO $dto */
        $analog = $this->analogRepository->findOneBy(['externalId' => $dto->id]);
        if (!$analog) {
            $this->logger->warning(
                "On update analog, analog with id '$dto->id' not found," .
                " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                return new ErrorMessage(
                    ErrorType::VALIDATION_ERROR,
                    'Post update analog, validation for group create failed: ' . $ex->getMessage()
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
                    "On update analog, element with code '$dto->elementCode' not found"
                );
            }
        }

        $analogProduct = $this->productRepository->findOneBy(['code' => $dto->analogCode]);
        $section = $this->sectionRepository->findOneBy(['code' => $dto->sectionCode]);
        if (!$section && !$analogProduct) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On update analog, section with code '$dto->sectionCode' or analog with code '$dto->analogCode' not found"
            );
        }

        $collectionCategory = new ArrayCollection();
        foreach ($dto->categoryName as $categoryName) {
            $categoryNameDocument = $this->categoryNameMapper->mapFromMessageDTO($categoryName);
            $collectionCategory->add($categoryNameDocument);
        }

        if (!$collectionCategory->isEmpty()) {
            $analog->setCategoryName($collectionCategory);
        }

        if ($element) {
            $analog->setElement($element);
        }

        $analog
            ->setAnalog($analogProduct)
            ->setSection($section);

        $this->logger->info("Analog with id '$dto->id' updated, message: " . json_encode($dto));

        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var AnalogMessageDTO $dto */
        $analog = $this->analogRepository->findOneBy(['externalId' => $dto->id]);
        if (!$analog) {
            return new ErrorMessage(
                ErrorType::ENTITY_NOT_FOUND,
                "On delete analog, analog with id '$dto->id' not found"
            );
        }

        $this->documentManager->remove($analog);

        $this->logger->info("Analog with id '$dto->id' deleted, message: " . json_encode($dto));

        return null;
    }
}