<?php

namespace App\Service\ProductImport;

use App\Dto\ProductDto;
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
    public function validate(ProductDto $productDto): void
    {
        $errors = $this->validator->validate($productDto);

        if (count($errors) > 0) {
            throw new ProductImportValidationException($errors);
        }
    }
}
