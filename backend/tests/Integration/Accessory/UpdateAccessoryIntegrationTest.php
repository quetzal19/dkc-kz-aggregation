<?php

namespace App\Tests\Integration\Accessory;

use App\Tests\Helper\Integration\{AccessoryHelper, CategoryNameHelper, ProductHelper, SectionHelper};
use App\Tests\Integration\AbstractAnalogAccessoryIntegrationTester;

class UpdateAccessoryIntegrationTest extends AbstractAnalogAccessoryIntegrationTester
{

    public function testSuccessUpdateAccessoryWithProducts(): void
    {
        $this->createAccessoryWithProducts();

        $analog = $this->assertAnalogAccessoryWithProduct(ProductHelper::CODE, ProductHelper::UPDATE_CODE, 'Accessory');

        $this->tester->assertEquals(AccessoryHelper::ACCESSORY_ID, $analog['externalId']);

        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::UPDATE_CODE,
            categories: $categories,
            accessoryCode: ProductHelper::CODE
        );

        $this->tester->assertNull($this->accessoryService->update($DTO));
        $this->documentManager->flush();

        $analog = $this->assertAnalogAccessoryWithProduct(ProductHelper::UPDATE_CODE, ProductHelper::CODE, 'Accessory');

        $this->tester->assertEquals(AccessoryHelper::ACCESSORY_ID, $analog['externalId']);
    }

    public function testSuccessUpdateAccessoryWithSection(): void
    {
        $this->createAccessoryWithSection();
        $this->createSection(code: SectionHelper::PARENT_CODE);

        $analog = $this->assertAnalogAccessoryWithSection(ProductHelper::CODE, SectionHelper::CODE, 'Accessory');
        $this->tester->assertEquals(AccessoryHelper::ACCESSORY_ID, $analog['externalId']);

        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::CODE,
            categories: $categories,
            sectionCode: SectionHelper::PARENT_CODE
        );

        $this->tester->assertNull($this->accessoryService->update($DTO));
        $this->documentManager->flush();

        $this->assertAnalogAccessoryWithSection(ProductHelper::CODE, SectionHelper::PARENT_CODE, 'Accessory');
    }

    public function testFailureUpdateAccessory(): void
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