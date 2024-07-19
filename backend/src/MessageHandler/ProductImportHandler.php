<?php

namespace App\MessageHandler;

use App\Document\Product;
use App\Dto\ProductDto;
use App\Message\ProductImport;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class ProductImportHandler
{
    protected const DATA_FORMAT = 'json';

    public function __construct(
        protected ValidatorInterface $validator,
        protected LoggerInterface $productImportLogger
    ) {
    }

    public function __invoke(ProductImport $message): void
    {
        try {
            $data = json_decode($message->getMessage(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            $this->productImportLogger->error($e->getMessage());

            return;
        }

        $productDto = new ProductDto($data);
        $errors = $this->validator->validate($productDto);

        if (count($errors) > 0) {
            // log errors
            $this->productImportLogger->error($errors);

            return;
        }

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
    }
}
