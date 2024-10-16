<?php

namespace App\Tests\Integration\Product;

use App\Tests\Helper\Integration\{ProductHelper, SectionHelper};
use App\Tests\Integration\AbstractIntegrationTester;

class CreateProductIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessCreateWithSection(): void
    {
        $this->createSection();

        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID
        );

        $this->tester->assertNull($this->productService->create($DTO));
        $this->documentManager->flush();

        $product = $this->tester->grabFromCollection('Product', ['code' => $DTO->code]);

        $this->tester->assertNotNull($product);
    }

    public function testFailureCreateWithoutSection(): void
    {
        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID
        );

        $this->tester->assertNotNull($this->productService->create($DTO));

        $product = $this->tester->grabFromCollection('Product', ['code' => $DTO->code]);

        $this->tester->assertNull($product);
    }

    public function testFailureCreateWithAlreadyExistingProduct(): void
    {
        $this->createProduct();

        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID
        );

        $this->tester->assertNotNull($this->productService->create($DTO));
    }
}