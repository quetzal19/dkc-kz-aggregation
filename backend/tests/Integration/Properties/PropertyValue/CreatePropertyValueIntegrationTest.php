<?php

namespace App\Tests\Integration\Properties\PropertyValue;

use App\Tests\Helper\Integration\Properties\{PropertyNameHelper, PropertyValueHelper};
use App\Tests\Integration\AbstractIntegrationTester;

class CreatePropertyValueIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessCreatePropertyValue(): void
    {
        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();
        $DTO = PropertyValueHelper::createPropertyValueMessageDTO($propertyNames);

        $this->tester->assertNull($this->propertyValueService->create($DTO));
        $this->documentManager->flush();

        $propertyValue = $this->tester->grabFromCollection('PropertyValue', ['code' => $DTO->code]);
        $this->tester->assertNotNull($propertyValue);
    }

    public function testFailCreatePropertyValue(): void
    {
        $this->createPropertyValue();

        $DTO = PropertyValueHelper::createPropertyValueMessageDTO([]);

        $this->tester->assertNotNull($this->propertyValueService->create($DTO));
    }
}