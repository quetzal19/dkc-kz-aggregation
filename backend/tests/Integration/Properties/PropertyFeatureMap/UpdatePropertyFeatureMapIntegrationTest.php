<?php

namespace App\Tests\Integration\Properties\PropertyFeatureMap;

use App\Tests\Helper\Integration\ProductHelper;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use App\Tests\Helper\Integration\Properties\{PropertyFeatureMapHelper,
    PropertyHelper,
    PropertyUnitHelper,
    PropertyValueHelper
};
use App\Tests\Integration\AbstractIntegrationTester;

class UpdatePropertyFeatureMapIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessUpdatePropertyFeatureMap(): void
    {
        $this->createProductFeatureMap();

        $DTO = PropertyFeatureMapHelper::createPropertyFeatureMapMessageDTO(
            unitCode: PropertyUnitHelper::UPDATED_CODE,
            etimArtClassId: ProductHelper::ART_CLASS_ID,
            featureCode: PropertyHelper::CODE,
        );

        $this->tester->assertNull($this->propertyFeatureMapService->update($DTO));
        $this->documentManager->flush();

        // Product property after update
        $product = $this->tester->grabFromCollection('Product', ['artClassId' => ProductHelper::ART_CLASS_ID]);

        $this->tester->assertNotNull($product);
        $this->tester->assertInstanceOf(BSONDocument::class, $product);

        $product = $product->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $product);

        $property = $product['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $property);

        $property = $property->getIterator()->current();

        $this->tester->assertEquals(
            PropertyHelper::CODE . ':' . PropertyValueHelper::CODE . ':' . PropertyUnitHelper::UPDATED_CODE,
            $property
        );

        // Property unit after update
        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertArrayHasKey('units', $property);

        $propertyUnit = $property['units'];

        $this->tester->assertInstanceOf(BSONArray::class, $propertyUnit);

        $propertyUnit = $propertyUnit->getArrayCopy();
        $propertyUnit = array_pop($propertyUnit);

        $this->tester->assertArrayHasKey('code', $propertyUnit);

        $this->tester->assertEquals(PropertyUnitHelper::UPDATED_CODE, $propertyUnit['code']);
    }


    public function testFailureUpdatePropertyFeatureMap(): void
    {
        $DTO = PropertyFeatureMapHelper::createPropertyFeatureMapMessageDTO(
            unitCode: PropertyUnitHelper::UPDATED_CODE,
            etimArtClassId: ProductHelper::ART_CLASS_ID,
            featureCode: PropertyHelper::CODE,
        );

        $this->tester->assertNotNull($this->propertyFeatureMapService->update($DTO));
        $this->documentManager->flush();

        $product = $this->tester->grabFromCollection('Product', ['artClassId' => ProductHelper::ART_CLASS_ID]);

        $this->tester->assertNull($product);

        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNull($property);
    }
}