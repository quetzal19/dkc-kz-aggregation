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

class CreatePropertyFeatureMapIntegrationTest extends AbstractIntegrationTester
{

    public function testCreatePropertyFeatureMap(): void
    {
        $this->createProductFeature();

        $DTO = PropertyFeatureMapHelper::createPropertyFeatureMapMessageDTO(
            unitCode: PropertyUnitHelper::CODE,
            etimArtClassId: ProductHelper::ART_CLASS_ID,
            featureCode: PropertyHelper::CODE,
        );

        $this->tester->assertNull($this->propertyFeatureMapService->create($DTO));
        $this->documentManager->flush();


        // Test unit code in product->property
        $product = $this->tester->grabFromCollection('Product', ['code' => ProductHelper::CODE]);

        $this->tester->assertNotNull($product);
        $this->tester->assertInstanceOf(BSONDocument::class, $product);

        $product = $product->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $product);

        $property = $product['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $property);

        $property = $property->getIterator()->current();

        $this->tester->assertEquals(
            PropertyHelper::CODE . ":" . PropertyValueHelper::CODE . ":" . PropertyUnitHelper::CODE,
            $property
        );

        // Test unit code in property->units
        $property = $this->tester->grabFromCollection('Property', ['code' => $DTO->primaryKeys->featureCode]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertArrayHasKey('units', $property);

        $propertyUnit = $property['units'];

        $this->tester->assertInstanceOf(BSONArray::class, $propertyUnit);

        $propertyUnit = $propertyUnit->getIterator()->current();


        $this->tester->assertInstanceOf(BSONDocument::class, $propertyUnit);

        $propertyUnit = $propertyUnit->getArrayCopy();

        $this->tester->assertArrayHasKey('code', $propertyUnit);
        $this->tester->assertEquals(PropertyUnitHelper::CODE, $propertyUnit['code']);
    }

    public function testFailureCreatePropertyFeatureMap(): void
    {
        $this->createProperty();

        $DTO = PropertyFeatureMapHelper::createPropertyFeatureMapMessageDTO(
            unitCode: PropertyUnitHelper::CODE,
            etimArtClassId: ProductHelper::ART_CLASS_ID,
            featureCode: PropertyHelper::CODE,
        );

        $this->tester->assertNotNull($this->propertyFeatureMapService->create($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => $DTO->primaryKeys->featureCode]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();;

        $this->tester->assertArrayNotHasKey('units', $property);
    }
}