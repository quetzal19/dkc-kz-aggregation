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
    }

    public function testFailureCreateWithoutSection(): void
    {
        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID
        );

        $this->tester->assertNotNull($this->productService->create($DTO));
    }

    public function testFailureCreateWithAlreadyExistingProduct()
    {
        $this->createProduct();

        $DTO = ProductHelper::createProductMessageDTO(
            SectionHelper::EXTERNAL_ID
        );

        $this->tester->assertNotNull($this->productService->create($DTO));
    }
}