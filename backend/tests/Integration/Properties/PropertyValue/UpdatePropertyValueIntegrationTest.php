<?php

namespace App\Tests\Integration\Properties\PropertyValue;

use App\Tests\Helper\Integration\Properties\PropertyValueHelper;
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\BSONDocument;

class UpdatePropertyValueIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessUpdatePropertyValue(): void
    {
        $this->createPropertyValue();

        $DTO = PropertyValueHelper::createPropertyValueMessageDTO([]);

        $this->tester->assertNull($this->propertyValueService->update($DTO));
        $this->documentManager->flush();

        $propertyValue = $this->tester->grabFromCollection('PropertyValue', ['code' => $DTO->code]);

        $this->tester->assertInstanceOf(BSONDocument::class, $propertyValue);

        $propertyValue = $propertyValue->getArrayCopy();

        $this->tester->assertArrayHasKey('code', $propertyValue);

        $this->tester->assertEquals($DTO->code, $propertyValue['code']);
    }

    public function testFailUpdatePropertyValue(): void
    {
        $DTO = PropertyValueHelper::createPropertyValueMessageDTO([]);

        $this->tester->assertNotNull($this->propertyValueService->update($DTO));
    }
}