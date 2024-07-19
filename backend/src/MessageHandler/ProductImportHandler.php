<?php

namespace App\MessageHandler;

use App\Dto\Product;
use App\Message\ProductImport;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductImportHandler
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected ValidatorInterface $validator,
        protected LoggerInterface $productImportLogger
    ) {
    }

    public function __invoke(ProductImport $message): void
    {
        $jsonData = $message->getMessage();

        // Deserialize JSON to object
        $myData = $this->serializer->deserialize($jsonData, Product::class, 'json');

        // Validate the object
        $errors = $this->validator->validate($myData);

        if (count($errors) > 0) {
            // log errors
        }
    }
}
