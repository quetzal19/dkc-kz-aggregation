<?php

namespace App\Features\Product\Service;

use App\Features\Product\DTO\Message\ProductMessageDTO;
use App\Features\Product\Mapper\ProductMapper;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\Enum\LocaleType;
use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class ProductActionService implements ActionInterface
{
    /**
     * @param ProductMapper $productMapper
     */
    public function __construct(
        private LoggerInterface $logger,
        private DocumentManager $documentManager,
        private ProductRepository $productRepository,
        private SectionRepository $sectionRepository,
        #[Autowire(service: 'map.product.mapper')]
        private MapperMessageInterface $productMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): void
    {
        /** @var ProductMessageDTO $dto */
        $product = $this->productRepository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)
        ]);

        if ($product) {
            $this->logger->error(
                "On create product with code '$dto->code' and locale '$dto->locale' product already exists," .
                " message: " . json_encode($dto)
            );
            return;
        }

        $section = $this->sectionRepository->findOneBy([
            'externalId' => $dto->sectionId,
        ]);

        if (!$section) {
            $this->logger->error(
                "On create product, section with externalId '$dto->sectionId' not found," .
                " message: " . json_encode($dto)
            );
            return;
        }

        $product = $this->productMapper->mapFromMessageDTO($dto);

        $product->setSection($section);

        $this->documentManager->persist($product);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->logger->info("Product with code '$dto->code' and locale '$dto->locale' created");
    }

    public function update(MessageDTOInterface $dto): void
    {
        /** @var ProductMessageDTO $dto */
        $product = $this->productRepository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$product) {
            $this->logger->error(
                "On update product with code '$dto->code' and locale '$dto->locale' product not found," .
                " message: " . json_encode($dto)
            );
            return;
        }

        if (!empty($dto->sectionId)) {
            $section = $this->sectionRepository->findOneBy([
                'externalId' => $dto->sectionId,
            ]);

            if (!$section) {
                $this->logger->error(
                    "On create product, section with externalId '$dto->sectionId' not found," .
                    " message: " . json_encode($dto)
                );
                return;
            }
        }

        $this->productMapper->mapFromMessageDTO($dto, $product);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->logger->info("Product with code '$dto->code' and locale '$dto->locale' updated");
    }

    public function delete(MessageDTOInterface $dto): void
    {
        /** @var ProductMessageDTO $dto */
        $product = $this->productRepository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$product) {
            $this->logger->error(
                "On delete product with code '$dto->code' and locale '$dto->locale' product not found," .
                " message: " . json_encode($dto)
            );
            return;
        }

        $this->documentManager->remove($product);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->logger->info("Product with code '$dto->code' and locale '$dto->locale' deleted");
    }
}