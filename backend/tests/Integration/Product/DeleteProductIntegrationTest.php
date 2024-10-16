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
        $this->documentManager->flush();

        $product = $this->tester->grabFromCollection('Product', ['code' => $DTO->code]);

        $this->tester->assertNull($product);
    }

    public function testFailureDeleteProduct(): void
    {
        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID,
        );

        $this->tester->assertNotNull($this->productService->delete($DTO));

        $product = $this->tester->grabFromCollection('Product', ['code' => $DTO->code]);

        $this->tester->assertNull($product);
    }
}