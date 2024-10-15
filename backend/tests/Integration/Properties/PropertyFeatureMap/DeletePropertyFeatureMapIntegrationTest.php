<?php

namespace App\Tests\Integration\Properties\PropertyFeatureMap;

use App\Tests\Helper\Integration\ProductHelper;
use App\Tests\Helper\Integration\Properties\{PropertyFeatureMapHelper,
    PropertyHelper,
    PropertyUnitHelper,
    PropertyValueHelper
};
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\{BSONArray, BSONDocument};

class DeletePropertyFeatureMapIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessDeletePropertyFeatureMap(): void
    {
        $this->createProductFeatureMap();

        $DTO = PropertyFeatureMapHelper::createPropertyFeatureMapMessageDTO(
            unitCode: PropertyUnitHelper::CODE,
            etimArtClassId: ProductHelper::ART_CLASS_ID,
            featureCode: PropertyHelper::CODE,
        );

        $this->tester->assertNull($this->propertyFeatureMapService->delete($DTO));
        $this->documentManager->flush();

        // Test product->property deleted unit code
        $product = $this->tester->grabFromCollection('Product', ['artClassId' => ProductHelper::ART_CLASS_ID]);

        $this->tester->assertNotNull($product);
        $this->tester->assertInstanceOf(BSONDocument::class, $product);

        $product = $product->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $product);

        $propertyProduct = $product['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $propertyProduct);

        $propertyProduct = $propertyProduct->getIterator()->current();

        $this->tester->assertEquals(PropertyHelper::CODE . ':' . PropertyValueHelper::CODE . ':', $propertyProduct);

        // Test property deleted unit code
        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertArrayHasKey('units', $property);

        $units = $property['units'];

        $this->tester->assertInstanceOf(BSONArray::class, $units);

        $units = $units->getArrayCopy();

        $this->tester->assertEmpty($units);
    }

    public function testFailureDeletePropertyFeatureMap(): void
    {
        $DTO = PropertyFeatureMapHelper::createPropertyFeatureMapMessageDTO(
            unitCode: PropertyUnitHelper::CODE,
            etimArtClassId: ProductHelper::ART_CLASS_ID,
            featureCode: PropertyHelper::CODE,
        );

        $this->tester->assertNotNull($this->propertyFeatureMapService->delete($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNull($property);

        $product = $this->tester->grabFromCollection('Product', ['artClassId' => ProductHelper::ART_CLASS_ID]);

        $this->tester->assertNull($product);
    }
}