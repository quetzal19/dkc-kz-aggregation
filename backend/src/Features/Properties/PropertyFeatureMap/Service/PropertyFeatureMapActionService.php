<?php

namespace App\Features\Properties\PropertyFeatureMap\Service;

use App\Document\Product\Product;
use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Properties\PropertyFeatureMap\DTO\Message\PropertyFeatureMapMessageDTO;
use App\Helper\Interface\{ActionInterface, Message\MessageDTOInterface};
use App\Features\TempStorage\Error\Type\ErrorType;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Helper\Abstract\Error\AbstractErrorMessage;

final readonly class PropertyFeatureMapActionService implements ActionInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private PropertyRepository $propertyRepository,
        #[Autowire(service: 'monolog.logger.art_class_feature_map')]
        private LoggerInterface $logger,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyFeatureMapMessageDTO $dto */
        $msg = $this->setPropertiesToProductAndProperty($dto, 'create');
        if (!is_null($msg)) {
            return $msg;
        }

        $this->logger->info(
            "On create property feature map, unit with code " . $dto->unitCode
            . " added to property with code " . $dto->primaryKeys->featureCode .
            " and to product with artClass" . $dto->primaryKeys->etimArtClassId,
        );
        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyFeatureMapMessageDTO $dto */
        $msg = $this->setPropertiesToProductAndProperty($dto, 'update');
        if (!is_null($msg)) {
            return $msg;
        }

        $this->logger->info(
            "On update property feature map, unit with code " . $dto->unitCode
            . " updated to property with code " . $dto->primaryKeys->featureCode .
            " and to product with artClass" . $dto->primaryKeys->etimArtClassId,
        );
        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyFeatureMapMessageDTO $dto */
        $product = $this->productRepository->findOneBy([
            'artClassId' => $dto->primaryKeys->etimArtClassId
        ]);

        if (!$product) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On delete property feature map, product with artClass " . $dto->primaryKeys->etimArtClassId . " not found"
            );
        }

        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode
        ]);

        if (!$property) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On delete property feature map, property with code " . $dto->primaryKeys->featureCode . " not found"
            );
        }

        $property->removeUnit($dto->unitCode);

        $product->setProperties(feature: $property->getCode(), unit: '');

        $this->logger->info(
            "On delete property feature map, unit with code " . $dto->unitCode
            . " removed from property with code " . $dto->primaryKeys->featureCode
            . " and to product with artClass" . $dto->primaryKeys->etimArtClassId,
        );
        return null;
    }

    private function setPropertiesToProductAndProperty(
        MessageDTOInterface $dto,
        string $fromAction
    ): ?AbstractErrorMessage {
        /**
         * @var PropertyFeatureMapMessageDTO $dto
         * @var Product $product
         */
        $product = $this->productRepository->findOneBy([
            'artClassId' => $dto->primaryKeys->etimArtClassId
        ]);

        if (!$product) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On $fromAction property feature map, product with artClass " . $dto->primaryKeys->etimArtClassId . " not found"
            );
        }

        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode
        ]);

        if (!$property) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On $fromAction property feature map, property with code " . $dto->primaryKeys->featureCode . " not found"
            );
        }

        $property->addOrUpdateUnit($dto->unitCode);

        $product->setProperties(
            feature: $property->getCode(),
            unit: $dto->unitCode
        );

        return null;
    }
}