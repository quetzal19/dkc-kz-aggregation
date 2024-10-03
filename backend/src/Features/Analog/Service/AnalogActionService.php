<?php

namespace App\Features\Analog\Service;

use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Document\Analog\Analog;
use App\Features\Analog\DTO\Message\AnalogMessageDTO;
use App\Features\Analog\Repository\AnalogRepository;
use App\Features\Message\Service\MessageValidatorService;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Section\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class AnalogActionService implements ActionInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private SectionRepository $sectionRepository,
        private AnalogRepository $analogRepository,
        private DocumentManager $documentManager,
        private LoggerInterface $logger,
        #[Autowire(service: 'map.category.name.mapper')]
        private MapperMessageInterface $categoryNameMapper,
        private MessageValidatorService $messageValidatorService,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
    {
        /** @var AnalogMessageDTO $dto */
        $analogDocument = $this->analogRepository->findOneBy(['externalId' => $dto->id]);
        if ($analogDocument) {
            $this->logger->error(
                "On create analog, analog with id '$dto->id' already exists," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $element = $this->productRepository->findOneBy(['code' => $dto->elementCode]);
        if (!$element) {
            $this->logger->error(
                "On create analog, element with code '$dto->elementCode' not found," .
                " message: " . json_encode($dto)
            );
            return false;
        }
        $analog = $this->productRepository->findOneBy(['code' => $dto->analogCode]);
        $section = $this->sectionRepository->findOneBy(['code' => $dto->sectionCode]);
        if (!$section && !$analog) {
            $this->logger->error(
                "On create analog, section with code '$dto->sectionCode' or analog with code '$dto->analogCode' not found," .
                " message: " . json_encode($dto)
            );
            return false;
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

        return true;
    }

    public function update(MessageDTOInterface $dto): bool
    {
        /** @var AnalogMessageDTO $dto */
        $analog = $this->analogRepository->findOneBy(['externalId' => $dto->id]);
        if (!$analog) {
            $this->logger->error(
                "On update analog, analog with id '$dto->id' not found," .
                " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                $this->logger->error(
                    'Post update analog, validation for group create failed: ' . $ex->getMessage(
                    ) . ", message: " . json_encode($dto)
                );
                return false;
            }

            return $this->create($dto);
        }

        $element = null;
        if (!empty($dto->elementCode)) {
            $element = $this->productRepository->findOneBy(['code' => $dto->elementCode]);
            if (!$element) {
                $this->logger->error(
                    "On update analog, element with code '$dto->elementCode' not found," .
                    " message: " . json_encode($dto)
                );
                return false;
            }
        }

        $analogProduct = $this->productRepository->findOneBy(['code' => $dto->analogCode]);
        $section = $this->sectionRepository->findOneBy(['code' => $dto->sectionCode]);
        if (!$section && !$analogProduct) {
            $this->logger->error(
                "On update analog, section with code '$dto->sectionCode' or analog with code '$dto->analogCode' not found," .
                " message: " . json_encode($dto)
            );
            return false;
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

        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
    {
        /** @var AnalogMessageDTO $dto */
        $analog = $this->analogRepository->findOneBy(['externalId' => $dto->id]);
        if (!$analog) {
            $this->logger->error(
                "On delete analog, analog with id '$dto->id' not found," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $this->documentManager->remove($analog);

        $this->logger->info("Analog with id '$dto->id' deleted, message: " . json_encode($dto));

        return true;
    }
}