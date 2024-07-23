<?php

namespace App\MessageHandler;

use App\Document\Product;
use App\Message\ProductImport;
use App\Service\ProductImport\ProductImportValidationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductImportHandler
{
    protected const DATA_FORMAT = 'json';

    public function __construct(
        protected ProductImportValidationService $productImportValidationService,
        protected LoggerInterface $productImportLogger
    ) {
    }

    public function __invoke(ProductImport $message): void
    {
        try {
            $data = json_decode($message->getMessage(), true, 512, JSON_THROW_ON_ERROR);

            $productDto = $this->productImportValidationService->validate($data);

            $product = new Product();
            $product->setCode($productDto->getCode());
            $product->setSectionCode($productDto->getSectionCode());
            $product->setName($productDto->getName());
            $product->setWeight($productDto->getWeight());
            $product->setVolume($productDto->getVolume());
            $product->setFilters($productDto->getFilters());

            $this->productImportLogger->info(
                'Product import: ' . $product->getName() . ' | filters: ' . json_encode($product->getFilters())
            );
        } catch (\Exception $e) {
            $this->productImportLogger->error($e->getMessage());

            return;
        }
    }
}
