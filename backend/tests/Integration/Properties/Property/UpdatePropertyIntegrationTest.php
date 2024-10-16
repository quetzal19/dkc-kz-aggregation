<?php

namespace App\Tests\Integration\Properties\Property;

use App\Tests\Helper\Integration\Properties\PropertyHelper;
use App\Tests\Helper\Integration\Properties\PropertyNameHelper;
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

class UpdatePropertyIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessUpdateProperty(): void
    {
        $this->createProperty();

        $propertyNames = PropertyNameHelper::initPropertyNameByLocales(PropertyNameHelper::UPDATE_NAME);
        $DTO = PropertyHelper::createPropertyMessageDTO($propertyNames);

        $this->tester->assertNull($this->propertyService->update($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => $DTO->code]);

        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertArrayHasKey('names', $property);

        /** @var BSONArray $propertyNames */
        $propertyNames = $property['names'];

        $this->tester->assertInstanceOf(BSONArray::class, $propertyNames);

        $propertyNamesUpdated = $propertyNames->getArrayCopy();
        $propertyName = array_pop($propertyNamesUpdated);

        $this->tester->assertInstanceOf(BSONDocument::class, $propertyName);
        $propertyName = $propertyName->getArrayCopy();

        $this->tester->assertArrayHasKey('name', $propertyName);
        $this->tester->assertEquals(PropertyNameHelper::UPDATE_NAME, $propertyName['name']);

        $this->tester->assertEquals($DTO->code, $property['code']);
    }

    public function testFailureUpdateProperty(): void
    {
        $DTO = PropertyHelper::createPropertyMessageDTO([]);

        $this->tester->assertNotNull($this->propertyService->update($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => $DTO->code]);

        $this->tester->assertNull($property);
    }
}