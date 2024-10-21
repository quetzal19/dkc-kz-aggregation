<?php

namespace App\Tests\Integration\Analog;

use App\Tests\Helper\Integration\{AnalogHelper, CategoryNameHelper, ProductHelper, SectionHelper};
use App\Tests\Integration\AbstractAnalogAccessoryIntegrationTester;
use MongoDB\Model\BSONDocument;

class CreateAnalogIntegrationTest extends AbstractAnalogAccessoryIntegrationTester
{
    public function testSuccessCreateAnalogWithProduct(): void
    {
        $this->createProduct(code: ProductHelper::UPDATE_CODE);
        $this->createProduct();

        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::CODE,
            categories: $categories,
            analogElementCode: ProductHelper::UPDATE_CODE
        );

        $this->tester->assertNull($this->analogService->create($DTO));
        $this->documentManager->flush();

        $elementId = $this->assertProductSection(ProductHelper::CODE);
        $analogId = $this->assertProductSection(ProductHelper::UPDATE_CODE);

        $analog = $this->tester->grabFromCollection('Analog', ['analog.$id' => $analogId, 'element.$id' => $elementId]);

        $this->tester->assertNotNull($analog);
        $this->tester->assertInstanceOf(BSONDocument::class, $analog);

        $analog = $analog->getArrayCopy();

        $this->tester->assertEquals($analog['externalId'], $DTO->id);
    }

    public function testSuccessCreateAnalogWithSection(): void
    {
        $sectionCode = SectionHelper::PARENT_CODE;

        $this->createProduct();
        $this->createSection(code: $sectionCode);

        $categories = CategoryNameHelper::initCategoryNameByLocales();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::CODE,
            categories: $categories,
            sectionCode: $sectionCode
        );

        $this->tester->assertNull($this->analogService->create($DTO));
        $this->documentManager->flush();

        $elementId = $this->assertProductSection(ProductHelper::CODE);

        $section = $this->tester->grabFromCollection('Section', ['code' => $sectionCode]);

        $this->tester->assertNotNull($section);
        $this->tester->assertInstanceOf(BSONDocument::class, $section);

        $section = $section->getArrayCopy();

        $this->tester->assertArrayHasKey('code', $section);
        $this->tester->assertEquals($sectionCode, $section['code']);

        $analog = $this->tester->grabFromCollection(
            'Analog',
            ['section.$id' => $section['_id'], 'element.$id' => $elementId]
        );

        $this->tester->assertNotNull($analog);
        $this->tester->assertInstanceOf(BSONDocument::class, $analog);

        $analog = $analog->getArrayCopy();

        $this->tester->assertEquals($analog['externalId'], $DTO->id);
    }

    public function testFailureCreateAnalog(): void
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