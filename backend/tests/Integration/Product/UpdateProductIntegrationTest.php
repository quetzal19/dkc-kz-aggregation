<?php

namespace App\Tests\Integration\Product;

use App\Tests\Helper\Integration\ProductHelper;
use App\Tests\Helper\Integration\SectionHelper;
use App\Tests\Integration\AbstractIntegrationTester;

class UpdateProductIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessUpdateProduct(): void
    {
        $this->createProduct();

        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID,
            active: false,
            sort: ProductHelper::UPDATED_SORT,
        );

        $this->tester->assertNull($this->productService->update($DTO));
        $this->documentManager->flush();

        $product = $this->tester->grabFromCollection('Product', ['code' => $DTO->code]);

        $this->tester->assertArrayHasKey('sort', $product);
        $this->tester->assertArrayHasKey('active', $product);

        $this->tester->assertEquals($DTO->sort, $product['sort']);
        $this->tester->assertEquals($DTO->active, $product['active']);
    }

    public function testFailureUpdateProduct(): void
    {
        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID,
        );

        $this->tester->assertNotNull($this->productService->update($DTO));
    }
}