<?php

namespace App\Tests\Integration\Properties\PropertyUnit;

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use App\Tests\Helper\Integration\Properties\{PropertyHelper, PropertyUnitHelper};
use App\Tests\Integration\AbstractIntegrationTester;

class DeletePropertyUnitIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessDeletePropertyUnit(): void
    {
        $this->createPropertyUnit();

        $DTO = PropertyUnitHelper::createPropertyUnitMessageDTO([]);

        $this->tester->assertNull($this->propertyUnitService->delete($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertArrayHasKey('units', $property);

        $propertyUnits = $property['units'];

        $this->tester->assertInstanceOf(BSONArray::class, $propertyUnits);

        $propertyUnits = $propertyUnits->getArrayCopy();

        $this->tester->assertEmpty($propertyUnits);
    }

    public function testFailureDeletePropertyUnit(): void
    {
        $DTO = PropertyUnitHelper::createPropertyUnitMessageDTO([]);

        $this->tester->assertNotNull($this->propertyUnitService->delete($DTO));
        $this->documentManager->flush();
    }
}