<?php

namespace App\Features\Product\Service;

use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\Message\Service\MessageValidatorService;
use App\Features\Product\DTO\Message\ProductMessageDTO;
use App\Features\Product\Mapper\ProductMapper;
use App\Features\Product\Repository\ProductRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Features\TempStorage\Error\Type\ErrorType;
use App\Helper\Enum\LocaleType;
use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use App\Helper\Abstract\Error\AbstractErrorMessage;

final readonly class ProductActionService implements ActionInterface
{
    /**
     * @param ProductMapper $productMapper
     */
    public function __construct(
        #[Autowire(service: 'monolog.logger.product')]
        private LoggerInterface $logger,
        private DocumentManager $documentManager,
        private ProductRepository $productRepository,
        private SectionRepository $sectionRepository,
        private MessageValidatorService $messageValidatorService,
        #[Autowire(service: 'map.product.mapper')]
        private MapperMessageInterface $productMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var ProductMessageDTO $dto */
        $product = $this->productRepository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)
        ]);

        if ($product) {
            return new ErrorMessage(
                ErrorType::ENTITY_ALREADY_EXISTS,
                "On create product with code '$dto->code' and locale '$dto->locale' product already exists",
            );
        }

        if (empty($dto->sectionId)) {
            return new ErrorMessage(
                errorType: ErrorType::VALIDATION_ERROR,
                message: "On create product, section externalId is empty, message: " . json_encode($dto)
            );
        }

        $section = $this->sectionRepository->findOneBy([
            'externalId' => $dto->sectionId,
        ]);

        if (!$section) {
            return new ErrorMessage(
                errorType: ErrorType::DATA_NOT_READY,
                message: "On create product, section with externalId '$dto->sectionId' not found"
            );
        }

        $product = $this->productMapper->mapFromMessageDTO($dto);

        $product->setSection($section);

        $this->documentManager->persist($product);

        $this->logger->info("Product with code '$dto->code' and locale '$dto->locale' created");
        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var ProductMessageDTO $dto */
        $product = $this->productRepository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$product) {
            $this->logger->warning(
                "On update product with code '$dto->code' and locale '$dto->locale' product not found," .
                " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                return new ErrorMessage(
                    ErrorType::VALIDATION_ERROR,
                    'Post update product, validation for group create failed: ' . $ex->getMessage()
                );
            }

            return $this->create($dto);
        }

        if (!empty($dto->sectionId)) {
            $section = $this->sectionRepository->findOneBy([
                'externalId' => $dto->sectionId,
            ]);

            if (!$section) {
                return new ErrorMessage(
                    ErrorType::DATA_NOT_READY,
                    "On update product, section with externalId '$dto->sectionId' not found"
                );
            }
        }

        $this->productMapper->mapFromMessageDTO($dto, $product);

        $this->logger->info("Product with code '$dto->code' and locale '$dto->locale' updated");

        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var ProductMessageDTO $dto */
        $product = $this->productRepository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$product) {
            return new ErrorMessage(
                ErrorType::ENTITY_NOT_FOUND,
                "On delete product with code '$dto->code' and locale '$dto->locale' product not found"
            );
        }

        $this->documentManager->remove($product);

        $this->logger->info("Product with code '$dto->code' and locale '$dto->locale' deleted");

        return null;
    }
}