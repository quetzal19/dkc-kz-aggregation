<?php

namespace App\MessageHandler;

use App\Document\Product;
use App\Dto\Product\ProductDto;
use App\Message\ProductImport;
use App\Service\ProductImport\ProductImportValidationException;
use App\Service\ProductImport\ProductImportValidationService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Тестовый обработчик, предположительно должен быть удалён. Импорт товаров будет осуществляться по другому.
 */
#[AsMessageHandler]
class ProductImportHandler
{
    public function __construct(
        protected ProductImportValidationService $productImportValidationService,
        protected LoggerInterface $productImportLogger,
        protected DocumentManager $documentManager
    ) {
    }

    public function __invoke(ProductImport $message): void
    {
        try {
            $data = json_decode($message->getMessage(), true, 512, JSON_THROW_ON_ERROR);
            $productDto = new ProductDto($data);

            $this->productImportValidationService->validateDto($productDto);

            $product = Product::fromDto($productDto);

            $this->productImportValidationService->validate($product);

            $this->documentManager->persist($product);
            $this->documentManager->flush();

            $this->productImportLogger->info(
                sprintf("Product import: %s | filters: %s", $product->getName(), json_encode($product->getFilters()))
            );
        } catch (ProductImportValidationException $e) {
            $this->productImportLogger->error(sprintf("Product import validation error: %s", $e->getMessage()));
        } catch (JsonException $e) {
            $this->productImportLogger->error(sprintf("Product import JSON error: %s", $e->getMessage()));
        } catch (Exception $e) {
            $this->productImportLogger->error(sprintf("Product import unexpected error: %s", $e->getMessage()));
        }
    }
}
