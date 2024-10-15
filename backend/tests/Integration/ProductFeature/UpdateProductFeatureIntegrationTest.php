<?php

namespace App\Tests\Integration\ProductFeature;

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use App\Tests\Helper\Integration\{ProductFeatureHelper,
    ProductHelper,
    Properties\PropertyHelper,
    Properties\PropertyValueHelper
};
use App\Tests\Integration\AbstractIntegrationTester;

class UpdateProductFeatureIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessUpdateProductFeature(): void
    {
        $this->createProductFeature();

        // Before update
        $productBeforeUpdate = $this->tester->grabFromCollection('Product', ['code' => ProductHelper::CODE]);

        $this->tester->assertNotNull($productBeforeUpdate);
        $this->tester->assertInstanceOf(BSONDocument::class, $productBeforeUpdate);

        $product = $productBeforeUpdate->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $product);
        $property = $product['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $property);
        $property = $property->getIterator()->current();

        $this->tester->assertEquals(PropertyHelper::CODE . ":" . PropertyValueHelper::CODE . ":", $property);


        $this->createPropertyValue(PropertyValueHelper::UPDATED_CODE);
        $this->createProperty(PropertyHelper::UPDATED_CODE);

        $DTO = ProductFeatureHelper::createProductFeatureMessageDTO(
            productCode: ProductHelper::CODE,
            featureCode: PropertyHelper::UPDATED_CODE,
            valueCode: PropertyValueHelper::UPDATED_CODE,
        );

        // Update properties product
        $this->tester->assertNull($this->productFeatureService->update($DTO));
        $this->documentManager->flush();

        // After update
        $updatedProductProperties = $this->tester->grabFromCollection('Product', ['code' => ProductHelper::CODE]);

        $this->tester->assertNotNull($updatedProductProperties);
        $this->tester->assertInstanceOf(BSONDocument::class, $updatedProductProperties);

        $product = $updatedProductProperties->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $product);
        $property = $product['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $property);

        $property = $property->getArrayCopy();
        $property = array_pop($property);

        $this->tester->assertEquals("{$DTO->primaryKeys->featureCode}:$DTO->valueCode:", $property);
    }

    public function testFailureUpdateProductFeature(): void
    {
        $this->createProduct();

        $DTO = ProductFeatureHelper::createProductFeatureMessageDTO(
            productCode: ProductHelper::CODE,
            featureCode: PropertyHelper::CODE,
            valueCode: PropertyValueHelper::CODE,
        );

        $this->tester->assertNotNull($this->productFeatureService->update($DTO));

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