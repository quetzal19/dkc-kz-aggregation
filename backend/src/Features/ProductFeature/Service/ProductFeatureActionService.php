<?php

namespace App\Features\ProductFeature\Service;

use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Document\Product\Product;
use App\Document\Properties\{Property, PropertyValue};
use App\Features\Product\Repository\ProductRepository;
use App\Features\ProductFeature\DTO\Message\ProductFeatureMessageDTO;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Properties\PropertyValue\Builder\Message\PropertyValueMessageDTOBuilder;
use App\Features\Properties\PropertyValue\Repository\PropertyValueRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class ProductFeatureActionService implements ActionInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private DocumentManager $documentManager,
        private ProductRepository $productRepository,
        private PropertyRepository $propertyRepository,
        private PropertyValueRepository $propertyValueRepository,
        #[Autowire(service: 'map.property.value.mapper')]
        private MapperMessageInterface $propertyValueMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
    {
        /** @var ProductFeatureMessageDTO $dto */
        if (!$this->setPropertiesProduct($dto, 'create')) {
            return false;
        }

        try {
            $this->documentManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return false;
        }

        $this->logger->info(
            "Product feature, product with code " . $dto->primaryKeys->productCode
            . " and property with code " . $dto->primaryKeys->featureCode . " created",
        );

        return true;
    }

    public function update(MessageDTOInterface $dto): bool
    {
        /** @var ProductFeatureMessageDTO $dto */
        if (!$this->setPropertiesProduct($dto, 'update')) {
            return false;
        }

        try {
            $this->documentManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return false;
        }

        $this->logger->info(
            "Product feature, product with code " . $dto->primaryKeys->productCode
            . " and property with code " . $dto->primaryKeys->featureCode . " updated",
        );

        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
    {
        /** @var ProductFeatureMessageDTO $dto */
        $property = $this->getProperty($dto, 'delete');
        $product = $this->getProduct($dto, 'delete');

        if (!$property || !$product) {
            return false;
        }

        $product->removePropertyByFeature($property->getCode());

        try {
            $this->documentManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return false;
        }

        $this->logger->info(
            "Product feature, product with code " . $dto->primaryKeys->productCode
            . " and property with code " . $dto->primaryKeys->featureCode . " deleted"
        );

        return true;
    }

    private function setPropertiesProduct(ProductFeatureMessageDTO $dto, string $fromAction): bool
    {
        /** @var PropertyValue $propertyValue */
        if (!empty($dto->valueCode)) {
            $propertyValue = $this->propertyValueRepository->findOneBy([
                'code' => $dto->valueCode,
            ]);

            if (!$propertyValue) {
                $this->logger->error(
                    "On $fromAction product feature, propertyValue with code $dto->valueCode not found,"
                    . " message: " . json_encode($dto),
                );
                return false;
            }
        } elseif (!empty($dto->value)) {
            $propertyValue = $this->propertyValueRepository->findOneBy([
                'value' => $dto->valueCode,
                'code' => null
            ]);

            if (!$propertyValue) {
                $propertyValueDTO = PropertyValueMessageDTOBuilder::create()
                    ->initializeLocalesByValue($dto->value)
                    ->build();

                $propertyValue = $this->propertyValueMapper->mapFromMessageDTO($propertyValueDTO);
            }
        } else {
            $this->logger->error(
                "On $fromAction product feature 'value' and 'valueCode' is empty, message: " . json_encode($dto),
            );
            return false;
        }

        $product = $this->getProduct($dto, $fromAction);
        if (!$product) {
            return false;
        }

        $property = $this->getProperty($dto, $fromAction);
        if (!$property) {
            return false;
        }

        if (!$this->documentManager->contains($propertyValue)) {
            $this->documentManager->persist($propertyValue);
        }

        $product->setProperties(
            feature: $property->getCode(),
            value: $propertyValue->getCodeOrId()
        );

        return true;
    }

    private function getProperty(ProductFeatureMessageDTO $dto, string $fromAction): ?Property
    {
        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode,
        ]);

        if (!$property) {
            $this->logger->error(
                "On $fromAction product feature, property with code " . $dto->primaryKeys->featureCode . " not found,"
                . " message: " . json_encode($dto),
            );
            return null;
        }

        return $property;
    }

    private function getProduct(ProductFeatureMessageDTO $dto, string $fromAction): ?Product
    {
        $product = $this->productRepository->findOneBy([
            'code' => $dto->primaryKeys->productCode,
        ]);

        if (!$product) {
            $this->logger->error(
                "On $fromAction product feature, product with code " . $dto->primaryKeys->productCode . " not found,"
                . " message: " . json_encode($dto),
            );
            return null;
        }

        return $product;
    }
}