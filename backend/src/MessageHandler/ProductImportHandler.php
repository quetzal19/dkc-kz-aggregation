<?php

namespace App\MessageHandler;

use App\Dto\Product;
use App\Message\ProductImport;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class ProductImportHandler
{
    protected const DATA_FORMAT = 'json';

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
        $myData = $this->serializer->deserialize($jsonData, Product::class, self::DATA_FORMAT);

        // Validate the object
        $errors = $this->validator->validate($myData);

        if (count($errors) > 0) {
            // log errors
            $this->productImportLogger->error($errors);
        }
    }
}
