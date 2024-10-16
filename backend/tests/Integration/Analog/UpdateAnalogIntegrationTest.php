<?php

namespace App\Tests\Integration\Analog;

use App\Tests\Helper\Integration\{AnalogHelper, CategoryNameHelper, ProductHelper, SectionHelper};
use App\Tests\Integration\AbstractAnalogAccessoryIntegrationTester;

class UpdateAnalogIntegrationTest extends AbstractAnalogAccessoryIntegrationTester
{

    public function testSuccessUpdateAnalogWithProduct(): void
    {
        $this->createAnalogWithProducts();

        $analog = $this->assertAnalogAccessoryWithProduct(ProductHelper::CODE, ProductHelper::UPDATE_CODE);

        $this->tester->assertEquals(AnalogHelper::ANALOG_ID, $analog['externalId']);

        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::UPDATE_CODE,
            categories: $categories,
            analogElementCode: ProductHelper::CODE
        );

        $this->tester->assertNull($this->analogService->update($DTO));
        $this->documentManager->flush();

        $analog = $this->assertAnalogAccessoryWithProduct(ProductHelper::UPDATE_CODE, ProductHelper::CODE);

        $this->tester->assertEquals(AnalogHelper::ANALOG_ID, $analog['externalId']);
    }

    public function testSuccessUpdateAnalogWithSection(): void
    {
        $this->createAnalogWithSection();
        $this->createSection(code: SectionHelper::PARENT_CODE);

        $analog = $this->assertAnalogAccessoryWithSection(ProductHelper::CODE, SectionHelper::CODE);
        $this->tester->assertEquals(AnalogHelper::ANALOG_ID, $analog['externalId']);

        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::CODE,
            categories: $categories,
            sectionCode: SectionHelper::PARENT_CODE
        );

        $this->tester->assertNull($this->analogService->update($DTO));
        $this->documentManager->flush();

        $this->assertAnalogAccessoryWithSection(ProductHelper::CODE, SectionHelper::PARENT_CODE);
    }

    public function testFailureUpdateAnalog(): void
    {
        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::CODE,
            categories: $categories,
            analogElementCode: ProductHelper::UPDATE_CODE
        );

        $this->tester->assertNotNull($this->analogService->create($DTO));

        $this->createProduct();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::CODE,
            analogElementCode: ProductHelper::UPDATE_CODE
        );

        $this->tester->assertNotNull($this->analogService->create($DTO));
    }
}