<?php

namespace App\Service\ProductImport;

use App\Document\Product;
use App\Dto\Product\ProductDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductImportValidationService
{
    public function __construct(protected ValidatorInterface $validator)
    {
    }

    /**
     * @param ProductDto $productDto
     * @return void
     * @throws ProductImportValidationException
     */
    public function validateDto(ProductDto $productDto): void
    {
        $errors = $this->validator->validate($productDto);

        if (count($errors) > 0) {
            throw new ProductImportValidationException($errors);
        }
    }

    // validate Product
    public function validate(Product $product): void
    {
        $errors = $this->validator->validate($product);

        if (count($errors) > 0) {
            throw new ProductImportValidationException($errors);
        }
    }
}
