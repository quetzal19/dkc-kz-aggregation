<?php

namespace App\Tests\Integration\Product;

use App\Tests\Helper\Integration\ProductHelper;
use App\Tests\Helper\Integration\SectionHelper;
use App\Tests\Integration\AbstractIntegrationTester;

class DeleteProductIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessDeleteProduct(): void
    {
        $this->createProduct();

        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID,
        );

        $this->tester->assertNull($this->productService->delete($DTO));
    }

    public function testFailureDeleteProduct(): void
    {
        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID,
        );

        $this->tester->assertNotNull($this->productService->delete($DTO));
    }
}