<?php

namespace App\Features\ProductFeature\Service;

use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\TempStorage\Error\Type\ErrorType;
use App\Document\Properties\{Property, PropertyValue};
use App\Features\Product\Repository\ProductRepository;
use App\Features\ProductFeature\DTO\Message\ProductFeatureMessageDTO;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Properties\PropertyValue\Builder\Message\PropertyValueMessageDTOBuilder;
use App\Features\Properties\PropertyValue\Repository\PropertyValueRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Helper\Abstract\Error\AbstractErrorMessage;

final readonly class ProductFeatureActionService implements ActionInterface
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.product_feature')]
        private LoggerInterface $logger,
        private DocumentManager $documentManager,
        private ProductRepository $productRepository,
        private PropertyRepository $propertyRepository,
        private PropertyValueRepository $propertyValueRepository,
        #[Autowire(service: 'map.property.value.mapper')]
        private MapperMessageInterface $propertyValueMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var ProductFeatureMessageDTO $dto */
        $msg = $this->setPropertiesProduct($dto, 'create');
        if (!is_null($msg)) {
            return $msg;
        }

        $this->logger->info(
            "Product feature, product with code " . $dto->primaryKeys->productCode
            . " and property with code " . $dto->primaryKeys->featureCode . " created",
        );

        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var ProductFeatureMessageDTO $dto */
        $msg = $this->setPropertiesProduct($dto, 'update');
        if (!is_null($msg)) {
            return $msg;
        }

        $this->logger->info(
            "Product feature, product with code " . $dto->primaryKeys->productCode
            . " and property with code " . $dto->primaryKeys->featureCode . " updated",
        );

        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var ProductFeatureMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode,
        ]);

        if (!$property) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On delete product feature, property with code " . $dto->primaryKeys->featureCode . " not found"
            );
        }

        $product = $this->productRepository->findOneBy([
            'code' => $dto->primaryKeys->productCode,
        ]);

        if (!$product) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On delete product feature, product with code " . $dto->primaryKeys->productCode . " not found"
            );
        }

        $product->removePropertyByFeature($property->getCode());

        $this->logger->info(
            "Product feature, product with code " . $dto->primaryKeys->productCode
            . " and property with code " . $dto->primaryKeys->featureCode . " deleted"
        );

        return null;
    }

    private function setPropertiesProduct(ProductFeatureMessageDTO $dto, string $fromAction): ?AbstractErrorMessage
    {
        /** @var PropertyValue $propertyValue */
        if (!empty($dto->valueCode)) {
            $propertyValue = $this->propertyValueRepository->findOneBy([
                'code' => $dto->valueCode,
            ]);

            if (!$propertyValue) {
                return new ErrorMessage(
                    ErrorType::DATA_NOT_READY,
                    "On $fromAction product feature, propertyValue with code $dto->valueCode not found"
                );
            }
        } elseif (!empty($dto->value)) {
            $propertyValue = $this->propertyValueRepository->findOneBy([
                'names.name' => $dto->value,
                'code' => null
            ]);

            if (!$propertyValue) {
                $propertyValueDTO = PropertyValueMessageDTOBuilder::create()
                    ->initializeLocalesByValue($dto->value)
                    ->build();

                $propertyValue = $this->propertyValueMapper->mapFromMessageDTO($propertyValueDTO);
            }
        } else {
            return new ErrorMessage(
                ErrorType::VALIDATION_ERROR,
                "On $fromAction product feature 'value' and 'valueCode' is empty, message: " . json_encode($dto)
            );
        }

        $product = $this->productRepository->findOneBy([
            'code' => $dto->primaryKeys->productCode,
        ]);

        if (!$product) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On $fromAction product feature, product with code " . $dto->primaryKeys->productCode . " not found"
            );
        }

        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode,
        ]);

        if (!$property) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On $fromAction product feature, property with code " . $dto->primaryKeys->featureCode . " not found"
            );
        }

        if (!$this->documentManager->contains($propertyValue)) {
            $this->documentManager->persist($propertyValue);
        }

        $product->setProperties(
            feature: $property->getCode(),
            value: $propertyValue->getCodeOrId()
        );

        return null;
    }
}