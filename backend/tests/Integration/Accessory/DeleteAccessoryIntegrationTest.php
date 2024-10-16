<?php

namespace App\Tests\Integration\Accessory;

use App\Tests\Helper\Integration\{AccessoryHelper, ProductHelper, SectionHelper};
use App\Tests\Integration\AbstractAnalogAccessoryIntegrationTester;

class DeleteAccessoryIntegrationTest extends AbstractAnalogAccessoryIntegrationTester
{
    public function testSuccessDeleteAccessoryWithProduct(): void
    {
        $this->createAccessoryWithProducts();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::CODE,
            accessoryCode: ProductHelper::UPDATE_CODE
        );

        $this->tester->assertNull($this->accessoryService->delete($DTO));
        $this->documentManager->flush();

        $analog = $this->assertAnalogAccessoryWithProduct(ProductHelper::CODE, ProductHelper::UPDATE_CODE, 'Accessory');

        $this->tester->assertArrayHasKey('isDeleted', $analog);

        $this->assertTrue($analog['isDeleted']);
    }

    public function testSuccessDeleteAccessoryWithSection(): void
    {
        $this->createAccessoryWithSection();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::CODE,
            sectionCode: SectionHelper::CODE
        );

        $this->tester->assertNull($this->accessoryService->delete($DTO));
        $this->documentManager->flush();

        $analog = $this->assertAnalogAccessoryWithSection(ProductHelper::CODE, SectionHelper::CODE, 'Accessory');

        $this->tester->assertArrayHasKey('isDeleted', $analog);

        $this->assertTrue($analog['isDeleted']);
    }

    public function testFailureDeleteAccessory(): void
    {
        $this->createSection();
        $this->createProduct();

        $DTO = AccessoryHelper::createAccessoryMessageDTO(
            productElementCode: ProductHelper::CODE,
            sectionCode: SectionHelper::CODE
        );

        $this->tester->assertNull($this->accessoryService->delete($DTO));
        $this->documentManager->flush();
    }
}