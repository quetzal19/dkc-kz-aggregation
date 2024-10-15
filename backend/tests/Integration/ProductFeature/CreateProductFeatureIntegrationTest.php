<?php

namespace App\Tests\Integration\ProductFeature;

use App\Tests\Helper\Integration\{ProductFeatureHelper,
    ProductHelper,
    Properties\PropertyHelper,
    Properties\PropertyValueHelper
};
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

class CreateProductFeatureIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessCreateProductFeature(): void
    {
        $this->createProduct();
        $this->createProperty();
        $this->createPropertyValue();

        $DTO = ProductFeatureHelper::createProductFeatureMessageDTO(
            productCode: ProductHelper::CODE,
            featureCode: PropertyHelper::CODE,
            valueCode: PropertyValueHelper::CODE,
        );

        $this->tester->assertNull($this->productFeatureService->create($DTO));
        $this->documentManager->flush();

        $updatedProductProperties = $this->tester->grabFromCollection('Product', ['code' => ProductHelper::CODE]);

        $this->tester->assertNotNull($updatedProductProperties);
        $this->tester->assertInstanceOf(BSONDocument::class, $updatedProductProperties);

        $updatedProductProperties = $updatedProductProperties->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $updatedProductProperties);

        $property = $updatedProductProperties['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $property);

        $property = $property->getIterator()->current();

        $this->tester->assertEquals("{$DTO->primaryKeys->featureCode}:$DTO->valueCode:", $property);
    }

    public function testFailCreateProductFeatureWithoutProperty(): void
    {
        $this->createProduct();
        $this->createPropertyValue();

        $DTO = ProductFeatureHelper::createProductFeatureMessageDTO(
            productCode: ProductHelper::CODE,
            featureCode: PropertyHelper::CODE,
            valueCode: PropertyValueHelper::CODE,
        );

        $this->tester->assertNotNull($this->productFeatureService->create($DTO));
        $this->documentManager->flush();

        $product = $this->tester->grabFromCollection('Product', ['code' => ProductHelper::CODE]);

        $this->tester->assertNotNull($product);
        $this->tester->assertInstanceOf(BSONDocument::class, $product);

        $product = $product->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $product);

        $property = $product['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertEmpty($property);
    }

    public function testFailCreateProductFeatureWithoutPropertyValue(): void
    {
        $this->createProduct();
        $this->createProperty();

        $DTO = ProductFeatureHelper::createProductFeatureMessageDTO(
            productCode: ProductHelper::CODE,
            featureCode: PropertyHelper::CODE,
            valueCode: PropertyValueHelper::CODE,
        );

        $this->tester->assertNotNull($this->productFeatureService->create($DTO));
        $this->documentManager->flush();

        $product = $this->tester->grabFromCollection('Product', ['code' => ProductHelper::CODE]);

        $this->tester->assertNotNull($product);
        $this->tester->assertInstanceOf(BSONDocument::class, $product);

        $product = $product->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $product);

        $property = $product['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertEmpty($property);
    }
}