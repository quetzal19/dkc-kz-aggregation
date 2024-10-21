<?php

namespace App\Tests\Integration\Properties\PropertyUnit;

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use App\Tests\Helper\Integration\Properties\{PropertyHelper, PropertyNameHelper, PropertyUnitHelper};
use App\Tests\Integration\AbstractIntegrationTester;

class UpdatePropertyUnitIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessUpdatePropertyUnit(): void
    {
        $this->createPropertyUnit();
        $this->createProductFeatureMap(PropertyUnitHelper::UPDATED_CODE);

        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();
        $DTO = PropertyUnitHelper::createPropertyUnitMessageDTO($propertyNames, PropertyUnitHelper::UPDATED_CODE);

        $this->tester->assertNull($this->propertyUnitService->update($DTO));
        $this->documentManager->flush();

        // Test get updated code from property->units
        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();
        $this->tester->assertArrayHasKey('units', $property);

        $propertyUnits = $property['units'];
        $this->tester->assertInstanceOf(BSONArray::class, $propertyUnits);

        $propertyUnits = $propertyUnits->getArrayCopy();
        $propertyUnit = array_pop($propertyUnits);

        $this->tester->assertInstanceOf(BSONDocument::class, $propertyUnit);

        $propertyUnit = $propertyUnit->getArrayCopy();

        $this->tester->assertArrayHasKey('code', $propertyUnit);
        $this->tester->assertEquals(PropertyUnitHelper::UPDATED_CODE, $propertyUnit['code']);
    }

    public function testFailureUpdatePropertyUnit(): void
    {
        $this->createPropertyUnit();

        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();
        $DTO = PropertyUnitHelper::createPropertyUnitMessageDTO($propertyNames, PropertyUnitHelper::UPDATED_CODE);

        $this->tester->assertNotNull($this->propertyUnitService->update($DTO));
        $this->documentManager->flush();

        // Test not equal updated code from property->units
        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();
        $this->tester->assertArrayHasKey('units', $property);

        $propertyUnits = $property['units'];
        $this->tester->assertInstanceOf(BSONArray::class, $propertyUnits);

        $propertyUnits = $propertyUnits->getArrayCopy();
        $propertyUnit = array_pop($propertyUnits);

        $this->tester->assertInstanceOf(BSONDocument::class, $propertyUnit);

        $propertyUnit = $propertyUnit->getArrayCopy();

        $this->tester->assertArrayHasKey('code', $propertyUnit);
        $this->tester->assertNotEquals(PropertyUnitHelper::UPDATED_CODE, $propertyUnit['code']);
    }
}