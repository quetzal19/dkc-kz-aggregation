<?php

namespace App\MessageHandler;

use App\Document\Product;
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
        $jsonData = $message->getMessage();

        // Validate the object
        $errors = $this->validator->validate($myData);

        if (count($errors) > 0) {
            // log errors
            $this->productImportLogger->error($errors);
        }
    }
}
