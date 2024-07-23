<?php

namespace App\Service\ProductImport;

use App\Dto\ProductDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductImportValidationService
{
    public function __construct(protected ValidatorInterface $validator)
    {
    }

    public function validate(array $data): ProductDto
    {
        $productDto = new ProductDto($data);
        $errors = $this->validator->validate($productDto);

        if (count($errors) > 0) {
            throw new ProductImportValidationException($errors);
        }

        return $productDto;
    }
}
