<?php

namespace App\Tests\Integration\Properties\PropertyValue;

use App\Tests\Helper\Integration\Properties\PropertyValueHelper;
use App\Tests\Integration\AbstractIntegrationTester;

class DeletePropertyValueIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessDeletePropertyValue(): void
    {
        $this->createPropertyValue();

        $DTO = PropertyValueHelper::createPropertyValueMessageDTO([]);

        $this->tester->assertNull($this->propertyValueService->delete($DTO));
        $this->documentManager->flush();

        $propertyValue = $this->tester->grabFromCollection('PropertyValue', ['code' => $DTO->code]);
        $this->tester->assertNull($propertyValue);
    }

    public function testFailureDeletePropertyValue(): void
    {
        $DTO = PropertyValueHelper::createPropertyValueMessageDTO([]);

        $this->tester->assertNotNull($this->propertyValueService->delete($DTO));

        $propertyValue = $this->tester->grabFromCollection('PropertyValue', ['code' => $DTO->code]);
        $this->tester->assertNull($propertyValue);
    }
}