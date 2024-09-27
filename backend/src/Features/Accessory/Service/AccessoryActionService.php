<?php

namespace App\Features\Accessory\Service;

use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Document\Accessory\Accessory;
use App\Features\Accessory\DTO\Message\AccessoryMessageDTO;
use App\Features\Accessory\Repository\AccessoryRepository;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Section\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class AccessoryActionService implements ActionInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private SectionRepository $sectionRepository,
        private AccessoryRepository $accessoryRepository,
        private DocumentManager $documentManager,
        private LoggerInterface $logger,
        #[Autowire(service: 'map.category.name.mapper')]
        private MapperMessageInterface $categoryNameMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
    {
        /** @var AccessoryMessageDTO $dto */
        $accessoryDocument = $this->accessoryRepository->findOneBy(['externalId' => $dto->id]);
        if ($accessoryDocument) {
            $this->logger->error(
                "On create accessory, accessory with external id '$dto->id' already exists," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $element = $this->productRepository->findOneBy(['code' => $dto->elementCode]);
        if (!$element) {
            $this->logger->error(
                "On create accessory, element with code '$dto->elementCode' not found," .
                " message: " . json_encode($dto)
            );
            return false;
        }
        $accessory = $this->productRepository->findOneBy(['code' => $dto->accessoryCode]);
        $section = $this->sectionRepository->findOneBy(['code' => $dto->sectionCode]);
        if (!$section && !$accessory) {
            $this->logger->error(
                "On create accessory, section with code '$dto->sectionCode' or accessory with code '$dto->accessoryCode' not found," .
                " message: " . json_encode($dto)
            );
            return false;
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

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        $this->logger->info("Accessory created Id: $dto->id. message: " . json_encode($dto));

        return true;
    }

    public function update(MessageDTOInterface $dto): bool
    {
        /**
         * @var AccessoryMessageDTO $dto
         * @var Accessory $accessoryDocument
         */
        $accessoryDocument = $this->accessoryRepository->findOneBy(['externalId' => $dto->id]);
        if (!$accessoryDocument) {
            $this->logger->error(
                "On update accessory, accessory with external id '$dto->id' not found," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $element = null;
        if (!empty($dto->elementCode)) {
            $element = $this->productRepository->findOneBy(['code' => $dto->elementCode]);
            if (!$element) {
                $this->logger->error(
                    "On update accessory, element with code '$dto->elementCode' not found," .
                    " message: " . json_encode($dto)
                );
                return false;
            }
        }

        $accessory = $this->productRepository->findOneBy(['code' => $dto->accessoryCode]);
        $section = $this->sectionRepository->findOneBy(['code' => $dto->sectionCode]);
        if (!$section && !$accessory) {
            $this->logger->error(
                "On update accessory, section with code '$dto->sectionCode' or accessory with code '$dto->accessoryCode' not found," .
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
            $accessoryDocument->setCategoryName($collectionCategory);
        }

        if ($element) {
            $accessoryDocument->setElement($element);
        }

        $accessoryDocument->setAccessory($accessory);
        $accessoryDocument->setSection($section);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        $this->logger->info("Accessory updated Id: $dto->id. message: " . json_encode($dto));

        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
    {
        /** @var AccessoryMessageDTO $dto */
        $accessoryDocument = $this->accessoryRepository->findOneBy(['externalId' => $dto->id]);
        if (!$accessoryDocument) {
            $this->logger->error(
                "On delete accessory, accessory with external id '$dto->id' not found," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $this->documentManager->remove($accessoryDocument);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        $this->logger->info("Accessory deleted Id: $dto->id. message: " . json_encode($dto));

        return true;
    }
}