<?php

namespace App\Tests\Integration\Analog;

use App\Tests\Helper\Integration\{AnalogHelper, ProductHelper, SectionHelper};
use App\Tests\Integration\AbstractAnalogAccessoryIntegrationTester;
use MongoDB\Model\BSONDocument;

class DeleteAnalogIntegrationTest extends AbstractAnalogAccessoryIntegrationTester
{

    public function testSuccessDeleteAnalogWithProduct(): void
    {
        $this->createAnalogWithProducts();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::CODE,
            analogElementCode: ProductHelper::UPDATE_CODE
        );

        $this->tester->assertNull($this->analogService->delete($DTO));
        $this->documentManager->flush();

        $elementId = $this->assertProductSection(ProductHelper::CODE);
        $analogId = $this->assertProductSection(ProductHelper::UPDATE_CODE);

        $analog = $this->tester->grabFromCollection('Analog', ['analog.$id' => $analogId, 'element.$id' => $elementId]);

        $this->tester->assertNotNull($analog);
        $this->tester->assertInstanceOf(BSONDocument::class, $analog);

        $analog = $analog->getArrayCopy();

        $this->tester->assertArrayHasKey('isDeleted', $analog);

        $this->assertTrue($analog['isDeleted']);
    }

    public function testSuccessDeleteAnalogWithSection(): void
    {
        $this->createAnalogWithSection();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::CODE,
            sectionCode: SectionHelper::CODE
        );

        $this->tester->assertNull($this->analogService->delete($DTO));
        $this->documentManager->flush();

        $elementId = $this->assertProductSection(ProductHelper::CODE);
        $sectionId = $this->assertProductSection(SectionHelper::CODE, 'Section');

        $analog = $this->tester->grabFromCollection('Analog', ['section.$id' => $sectionId, 'element.$id' => $elementId]);

        $this->tester->assertNotNull($analog);
        $this->tester->assertInstanceOf(BSONDocument::class, $analog);

        $analog = $analog->getArrayCopy();

        $this->tester->assertArrayHasKey('isDeleted', $analog);

        $this->assertTrue($analog['isDeleted']);
    }

    public function testFailureDeleteAnalog(): void
    {
        $this->createSection();
        $this->createProduct();

        $DTO = AnalogHelper::createAnalogMessageDTO(
            productElementCode: ProductHelper::CODE,
            sectionCode: SectionHelper::CODE
        );

        $this->tester->assertNull($this->analogService->delete($DTO));
        $this->documentManager->flush();
    }
}