<?php

namespace App\Tests\Integration\Properties\Property;

use App\Tests\Helper\Integration\Properties\PropertyHelper;
use App\Tests\Helper\Integration\Properties\PropertyNameHelper;
use App\Tests\Integration\AbstractIntegrationTester;

class CreatePropertyIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessCreateProperty(): void
    {
        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();
        $DTO = PropertyHelper::createPropertyMessageDTO($propertyNames);

        $this->tester->assertNull($this->propertyService->create($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => $DTO->code]);

        $this->tester->assertNotNull($property);
    }

    public function testFailureCreateProperty(): void
    {
        $this->createProperty();

        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();
        $DTO = PropertyHelper::createPropertyMessageDTO($propertyNames);

        $this->tester->assertNotNull($this->propertyService->create($DTO));
    }

}