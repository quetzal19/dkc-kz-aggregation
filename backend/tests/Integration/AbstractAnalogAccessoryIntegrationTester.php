<?php

namespace App\Tests\Integration;

use MongoDB\Model\BSONDocument;

class AbstractAnalogAccessoryIntegrationTester extends AbstractIntegrationTester
{
    protected function assertAnalogAccessoryWithSection(string $elementCode, string $sectionCode, string $collection = 'Analog'): array
    {
        $elementId = $this->assertProductSection($elementCode);
        $sectionId = $this->assertProductSection($sectionCode, 'Section');

        $analog = $this->tester->grabFromCollection($collection, ['section.$id' => $sectionId, 'element.$id' => $elementId]);

        $this->tester->assertNotNull($analog);
        $this->tester->assertInstanceOf(BSONDocument::class, $analog);

        return $analog->getArrayCopy();
    }

    protected function assertAnalogAccessoryWithProduct(string $elementCode, string $analogCode, string $collection = 'Analog'): array
    {
        $elementId = $this->assertProductSection($elementCode);
        $analogId = $this->assertProductSection($analogCode);

        $property = mb_strtolower($collection);
        $analog = $this->tester->grabFromCollection($collection, [ $property . '.$id' => $analogId, 'element.$id' => $elementId]);

        $this->tester->assertNotNull($analog);
        $this->tester->assertInstanceOf(BSONDocument::class, $analog);

        return $analog->getArrayCopy();
    }

    protected function assertProductSection(string $elementCode, string $collection = 'Product'): string
    {
        $productSection = $this->tester->grabFromCollection($collection, ['code' => $elementCode]);

        $this->tester->assertNotNull($productSection);
        $this->tester->assertInstanceOf(BSONDocument::class, $productSection);

        $productSection = $productSection->getArrayCopy();

        $this->tester->assertArrayHasKey('code', $productSection);
        $this->tester->assertEquals($elementCode, $productSection['code']);

        return $productSection['_id'];
    }
}