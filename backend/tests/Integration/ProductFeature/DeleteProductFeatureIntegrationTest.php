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

class DeleteProductFeatureIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessDeletePropertyFeature(): void
    {
        $this->createProductFeature();

        $DTO = ProductFeatureHelper::createProductFeatureMessageDTO(
            productCode: ProductHelper::CODE,
            featureCode: PropertyHelper::CODE,
            valueCode: PropertyValueHelper::CODE,
        );

        $this->tester->assertNull($this->productFeatureService->delete($DTO));
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

    public function testFailureDeleteProductFeature(): void
    {
        $this->createProductFeature();

        $DTO = ProductFeatureHelper::createProductFeatureMessageDTO(
            productCode: ProductHelper::CODE,
            featureCode: PropertyHelper::UPDATED_CODE,
            valueCode: PropertyValueHelper::CODE,
        );

        $this->tester->assertNotNull($this->productFeatureService->delete($DTO));
        $this->documentManager->flush();

        $product = $this->tester->grabFromCollection('Product', ['code' => ProductHelper::CODE]);

        $this->tester->assertNotNull($product);
        $this->tester->assertInstanceOf(BSONDocument::class, $product);

        $product = $product->getArrayCopy();

        $this->tester->assertArrayHasKey('property', $product);

        $property = $product['property'];

        $this->tester->assertInstanceOf(BSONArray::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertNotEmpty($property);
    }
}