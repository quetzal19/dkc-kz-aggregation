<?php

namespace App\Tests\Integration\Properties\Property;

use App\Tests\Helper\Integration\Properties\PropertyHelper;
use App\Tests\Integration\AbstractIntegrationTester;

class DeletePropertyIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessDeleteProperty(): void
    {
        $this->createProperty();

        $DTO = PropertyHelper::createPropertyMessageDTO([]);

        $this->tester->assertNull($this->propertyService->delete($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => $DTO->code]);

        $this->tester->assertNull($property);
    }

    public function testFailureDeleteProperty(): void
    {
        $DTO = PropertyHelper::createPropertyMessageDTO([]);

        $this->tester->assertNotNull($this->propertyService->delete($DTO));

        $property = $this->tester->grabFromCollection('Property', ['code' => $DTO->code]);

        $this->tester->assertNull($property);
    }
}