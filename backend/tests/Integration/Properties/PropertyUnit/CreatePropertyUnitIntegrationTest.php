<?php

namespace App\Tests\Integration\Properties\PropertyUnit;

use App\Tests\Helper\Integration\Properties\{PropertyHelper, PropertyNameHelper, PropertyUnitHelper};
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\{BSONArray, BSONDocument};

class CreatePropertyUnitIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessCreatePropertyUnit(): void
    {
        $this->createPropertyUnit();

        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();
        $DTO = PropertyUnitHelper::createPropertyUnitMessageDTO($propertyNames);

        $this->tester->assertNull($this->propertyUnitService->create($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertEquals(PropertyHelper::CODE, $property['code']);
        $this->tester->assertArrayHasKey('units', $property);

        $propertyUnits = $property['units'];

        $this->tester->assertInstanceOf(BSONArray::class, $propertyUnits);

        $propertyUnits = $propertyUnits->getIterator()->current();

        $this->tester->assertInstanceOf(BSONDocument::class, $propertyUnits);

        $propertyUnits = $propertyUnits->getArrayCopy();

        $this->tester->assertArrayHasKey('names', $propertyUnits);
        $propertyNames = $propertyUnits['names'];

        $this->tester->assertInstanceOf(BSONArray::class, $propertyNames);

        $propertyNames = $propertyNames->getIterator()->current()->getArrayCopy();

        $this->tester->assertArrayHasKey('name', $propertyNames);

        $propertyName = $propertyNames['name'];

        $this->tester->assertEquals(PropertyNameHelper::NAME, $propertyName);
    }

    public function testFailureCreatePropertyUnit(): void
    {
        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();
        $DTO = PropertyUnitHelper::createPropertyUnitMessageDTO($propertyNames);

        $this->tester->assertNotNull($this->propertyUnitService->create($DTO));
        $this->documentManager->flush();
    }

}