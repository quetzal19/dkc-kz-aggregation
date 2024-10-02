<?php

namespace App\Features\Properties\PropertyFeatureMap\Service;

use App\Document\Product\Product;
use App\Document\Properties\Property;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Properties\PropertyFeatureMap\DTO\Message\PropertyFeatureMapMessageDTO;
use App\Helper\Interface\{ActionInterface, Message\MessageDTOInterface};
use Psr\Log\LoggerInterface;

final readonly class PropertyFeatureMapActionService implements ActionInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private PropertyRepository $propertyRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
    {
        /** @var PropertyFeatureMapMessageDTO $dto */
        if (!$this->setPropertiesToProductAndProperty($dto, 'create')) {
            return false;
        }

        $this->logger->info(
            "On create property feature map, unit with code " . $dto->unitCode
            . " added to property with code " . $dto->primaryKeys->featureCode .
            " and to product with artClass" . $dto->primaryKeys->etimArtClassId,
        );
        return true;
    }

    public function update(MessageDTOInterface $dto): bool
    {
        /** @var PropertyFeatureMapMessageDTO $dto */
        if (!$this->setPropertiesToProductAndProperty($dto, 'update')) {
            return false;
        }

        $this->logger->info(
            "On update property feature map, unit with code " . $dto->unitCode
            . " updated to property with code " . $dto->primaryKeys->featureCode .
            " and to product with artClass" . $dto->primaryKeys->etimArtClassId,
        );
        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
    {
        /** @var PropertyFeatureMapMessageDTO $dto */
        $product = $this->getProduct($dto, 'delete');
        if (!$product) {
            return false;
        }

        $property = $this->getProperty($dto, 'delete');
        if (!$property) {
            return false;
        }

        $property->removeUnit($dto->unitCode);

        $product->setProperties(feature: $property->getCode(), unit: '');

        $this->logger->info(
            "On delete property feature map, unit with code " . $dto->unitCode
            . " removed from property with code " . $dto->primaryKeys->featureCode
            . " and to product with artClass" . $dto->primaryKeys->etimArtClassId,
        );
        return true;
    }

    private function setPropertiesToProductAndProperty(MessageDTOInterface $dto, string $fromAction): bool
    {
        /**
         * @var PropertyFeatureMapMessageDTO $dto
         * @var Product $product
         */
        $product = $this->getProduct($dto, $fromAction);
        if (!$product) {
            return false;
        }

        $property = $this->getProperty($dto, $fromAction);
        if (!$property) {
            return false;
        }

        $property->addOrUpdateUnit($dto->unitCode);

        $product->setProperties(
            feature: $property->getCode(),
            unit: $dto->unitCode
        );

        return true;
    }

    private function getProduct(PropertyFeatureMapMessageDTO $dto, string $fromAction): ?Product
    {
        $product = $this->productRepository->findOneBy([
            'artClassId' => $dto->primaryKeys->etimArtClassId
        ]);

        if (!$product) {
            $this->logger->error(
                "On $fromAction property feature map, product with artClass " . $dto->primaryKeys->etimArtClassId . " not found,"
                . " message: " . json_encode($dto),
            );
            return null;
        }

        return $product;
    }

    private function getProperty(PropertyFeatureMapMessageDTO $dto, string $fromAction): ?Property
    {
        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode
        ]);

        if (!$property) {
            $this->logger->error(
                "On $fromAction property feature map, property with code " . $dto->primaryKeys->featureCode . " not found,"
                . " message: " . json_encode($dto),
            );
            return null;
        }

        return $property;
    }
}