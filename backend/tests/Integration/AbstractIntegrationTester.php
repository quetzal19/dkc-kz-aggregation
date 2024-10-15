<?php

namespace App\Tests\Integration;

use App\Features\Product\Service\ProductActionService;
use App\Features\ProductFeature\Service\ProductFeatureActionService;
use App\Features\Properties\Property\Service\PropertyActionService;
use App\Features\Properties\PropertyFeatureMap\Service\PropertyFeatureMapActionService;
use App\Features\Properties\PropertyUnit\Service\PropertyUnitActionService;
use App\Features\Properties\PropertyValue\Service\PropertyValueActionService;
use App\Features\Section\Service\SectionActionService;
use App\Helper\Interface\ActionInterface;
use App\Tests\Helper\Integration\{ProductFeatureHelper,
    ProductHelper,
    Properties\PropertyFeatureMapHelper,
    Properties\PropertyHelper,
    Properties\PropertyNameHelper,
    Properties\PropertyUnitHelper,
    Properties\PropertyValueHelper,
    SectionHelper
};
use App\Tests\Support\IntegrationTester;
use Codeception\Test\Unit;
use Doctrine\ODM\MongoDB\DocumentManager;

class AbstractIntegrationTester extends Unit
{
    protected IntegrationTester $tester;
    protected DocumentManager $documentManager;

    protected ActionInterface $productService;
    protected ActionInterface $sectionService;
    protected ActionInterface $propertyValueService;
    protected ActionInterface $propertyService;
    protected ActionInterface $productFeatureService;
    protected ActionInterface $propertyFeatureMapService;
    protected ActionInterface $propertyUnitService;

    protected function _before(): void
    {
        $this->documentManager = $this->tester->grabService(DocumentManager::class);

        $this->productService = $this->tester->grabService(ProductActionService::class);
        $this->sectionService = $this->tester->grabService(SectionActionService::class);
        $this->propertyService = $this->tester->grabService(PropertyActionService::class);
        $this->propertyValueService = $this->tester->grabService(PropertyValueActionService::class);
        $this->productFeatureService = $this->tester->grabService(ProductFeatureActionService::class);
        $this->propertyFeatureMapService = $this->tester->grabService(PropertyFeatureMapActionService::class);
        $this->propertyUnitService = $this->tester->grabService(PropertyUnitActionService::class);
    }

    public function createPropertyFeatureMap(): void
    {
        $this->createProduct();
        $this->createProperty();

        $DTO = PropertyFeatureMapHelper::createPropertyFeatureMapMessageDTO(
            unitCode: PropertyUnitHelper::CODE,
            etimArtClassId: ProductHelper::ART_CLASS_ID,
            featureCode: PropertyHelper::CODE,
        );

        $this->assertNull($this->propertyFeatureMapService->create($DTO));
        $this->documentManager->flush();
    }

    public function createPropertyUnit(string $code = PropertyUnitHelper::CODE): void
    {
        $this->createProductFeatureMap();

        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();
        $DTO = PropertyUnitHelper::createPropertyUnitMessageDTO($propertyNames, $code);

        $this->tester->assertNull($this->propertyUnitService->create($DTO));
        $this->documentManager->flush();
    }

    public function createProductFeatureMap(string $unitCode = PropertyUnitHelper::CODE): void
    {
        $this->createProductFeature();

        $DTO = PropertyFeatureMapHelper::createPropertyFeatureMapMessageDTO(
            unitCode: $unitCode,
            etimArtClassId: ProductHelper::ART_CLASS_ID,
            featureCode: PropertyHelper::CODE,
        );

        $this->tester->assertNull($this->propertyFeatureMapService->update($DTO));
        $this->documentManager->flush();
    }

    protected function createProductFeature(): void
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
    }

    protected function createProperty(string $code = PropertyHelper::CODE): void
    {
        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();

        $DTO = PropertyHelper::createPropertyMessageDTO($propertyNames, $code);

        $property = $this->tester->grabFromCollection('Property', ['code' => $code]);

        if (!$property) {
            $this->tester->assertNull($this->propertyService->create($DTO));
            $this->documentManager->flush();
        }
    }

    protected function createPropertyValue(string $code = PropertyValueHelper::CODE): void
    {
        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();

        $DTO = PropertyValueHelper::createPropertyValueMessageDTO($propertyNames, $code);

        $propertyValue = $this->tester->grabFromCollection('PropertyValue', ['code' => $code]);
        if (!$propertyValue) {
            $this->tester->assertNull($this->propertyValueService->create($DTO));
            $this->documentManager->flush();
        }
    }

    protected function createProduct(): void
    {
        $this->createSection();

        $DTO = ProductHelper::createProductMessageDTO(SectionHelper::EXTERNAL_ID);

        $product = $this->tester->grabFromCollection('Product', ['code' => ProductHelper::CODE]);
        if (!$product) {
            $this->tester->assertNull($this->productService->create($DTO));
            $this->documentManager->flush();
        }
    }

    protected function createSection(
        string $name = SectionHelper::NAME,
        string $code = SectionHelper::CODE,
        string $externalId = SectionHelper::EXTERNAL_ID,
        ?string $parentId = null,
    ): void {
        $DTO = SectionHelper::createSectionMessageDTO(
            $name,
            $code,
            $externalId,
            $parentId
        );

        $section = $this->tester->grabFromCollection('Section', ['code' => $code]);
        if (!$section) {
            $this->tester->assertNull($this->sectionService->create($DTO));
            $this->documentManager->flush();
        }
    }

    protected function print($obj)
    {
        echo "\n\n";
        var_export($obj);
        echo "\n\n";
        exit();
    }
}