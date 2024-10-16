<?php

namespace App\Tests\Integration\Accessory;

use App\Tests\Helper\Integration\{AccessoryHelper, CategoryNameHelper, ProductHelper, SectionHelper};
use App\Tests\Integration\AbstractAnalogAccessoryIntegrationTester;

class CreateAccessoryIntegrationTest extends AbstractAnalogAccessoryIntegrationTester
{

    public function testSuccessCreateAccessoryWithProduct(): void
    {
        $this->createProduct(code: ProductHelper::UPDATE_CODE);
        $this->createProduct();

        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::CODE,
            categories: $categories,
            accessoryCode: ProductHelper::UPDATE_CODE
        );

        $this->tester->assertNull($this->accessoryService->create($DTO));
        $this->documentManager->flush();

        $analog = $this->assertAnalogAccessoryWithProduct(ProductHelper::CODE, ProductHelper::UPDATE_CODE, 'Accessory');

        $this->tester->assertEquals($analog['externalId'], $DTO->id);
    }

    public function testSuccessCreateAccessoryWithSection(): void
    {
        $code = SectionHelper::PARENT_CODE;

        $this->createProduct();
        $this->createSection(code: $code);

        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::CODE,
            categories: $categories,
            sectionCode: $code
        );

        $this->tester->assertNull($this->accessoryService->create($DTO));
        $this->documentManager->flush();

        $analog = $this->assertAnalogAccessoryWithSection(ProductHelper::CODE, $code, 'Accessory');

        $this->tester->assertEquals($analog['externalId'], $DTO->id);
    }

    public function testFailureCreateAccessory(): void
    {
        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::CODE,
            categories: $categories,
            accessoryCode: ProductHelper::UPDATE_CODE
        );

        $this->tester->assertNotNull($this->accessoryService->create($DTO));

        $this->createProduct();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::CODE,
            accessoryCode: ProductHelper::UPDATE_CODE
        );

        $this->tester->assertNotNull($this->accessoryService->create($DTO));
    }
}